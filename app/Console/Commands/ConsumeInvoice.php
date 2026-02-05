<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use App\Services\DocumentService;

class ConsumeInvoice extends Command
{
    protected $signature = 'rabbitmq:consume-invoice';
    protected $description = 'Consume RabbitMQ messages for invoice creation';
    protected $retryLimit = 3;

    public function handle()
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST'),
            env('RABBITMQ_PORT'),
            env('RABBITMQ_USER'),
            env('RABBITMQ_PASSWORD')
        );
        $channel = $connection->channel();

        // --- Configuración basada en tu publicador ---
        $mainExchange = 'sunat.exchange';
        $mainQueue = 'sunat.invoice.created';
        $mainRoutingKey = 'sunat.invoice.created';
        $dlQueue = 'sunat.invoice.dead-letter';
        // --- Fin de la configuración ---

        // Declaraciones
        $channel->exchange_declare($mainExchange, 'direct', false, true, false);

        // Declarar la cola principal y configurar su dead-lettering (apunta al exchange por defecto)
        $channel->queue_declare($mainQueue, false, true, false, false, false, [
            'x-dead-letter-exchange' => ['S', ''], // Exchange por defecto
            'x-dead-letter-routing-key' => ['S', $dlQueue],
        ]);

        // Declarar la cola de dead-letter
        $channel->queue_declare($dlQueue, false, true, false, false);

        // Enlazar la cola principal al exchange donde se publican los mensajes
        $channel->queue_bind($mainQueue, $mainExchange, $mainRoutingKey);

        $callback = function ($msg) use ($channel, $mainExchange, $mainRoutingKey, $dlQueue) {
            $data = json_decode($msg->body, true);
            Log::info('Mensaje recibido en consumidor:', $data);

            $retryCount = 0;
            $headers = $msg->get('application_headers');
            if ($headers instanceof AMQPTable && $headers->getNativeData()) {
                $retryCount = $headers->getNativeData()['x-retries'] ?? 0;
            }

            try {
                // Validar que venga el tenant_id
                if (!isset($data['tenant_id'])) {
                    throw new \Exception("Falta tenant_id en el mensaje");
                }

                // Buscar tenant en la DB central
                $tenant = Tenant::find($data['tenant_id']);
                if (!$tenant) {
                    throw new \Exception("Tenant {$data['tenant_id']} no encontrado en la base central");
                }

                // Inicializar tenant
                tenancy()->initialize($tenant);
                Log::info("Tenant inicializado correctamente", [
                    'tenant_id' => $tenant->id,
                    'db' => config('database.connections.tenant.database') // muestra a qué DB apunta
                ]);

                $documentService = app(DocumentService::class);
                $documentService->createBoleta($data);

                $msg->ack();
                Log::info('Mensaje procesado exitosamente', ['tenant' => $tenant->id]);
            } catch (\Exception $e) {
                Log::error('Error procesando el mensaje', [
                    'error' => $e->getMessage(),
                    'tenant_id' => $data['tenant_id'] ?? null,
                ]);

                $retryCount++;
                if ($retryCount <= $this->retryLimit) {
                    $newHeaders = new AMQPTable([
                        'x-retries' => $retryCount
                    ]);

                    $newMsg = new AMQPMessage($msg->body, [
                        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                        'application_headers' => $newHeaders,
                    ]);

                    $channel->basic_publish($newMsg, $mainExchange, $mainRoutingKey);
                    Log::warning(" Reintentando mensaje (retry #$retryCount)");
                } else {
                    $deadMsg = new AMQPMessage($msg->body, [
                        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                    ]);
                    $channel->basic_publish($deadMsg, '', $dlQueue);
                    Log::error("Mensaje enviado a dead-letter después de $retryCount intentos");
                }

                $msg->ack();
            } finally {
                // Muy importante: liberar el tenant para no contaminar otros mensajes
                tenancy()->end();
            }
        };

        $channel->basic_consume($mainQueue, '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
