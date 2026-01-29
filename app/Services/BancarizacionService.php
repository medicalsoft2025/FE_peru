<?php

namespace App\Services;

use App\Models\MedioPagoBancarizacion;
use Illuminate\Support\Facades\Log;

class BancarizacionService
{
    /**
     * Umbrales de bancarización según Ley N° 28194
     */
    const UMBRAL_PEN = 2000.00; // Soles
    const UMBRAL_USD = 500.00;  // Dólares

    /**
     * Determinar si una operación aplica bancarización
     *
     * @param float $montoTotal Monto total de la operación
     * @param string $moneda Código de moneda (PEN, USD)
     * @return bool
     */
    public function aplicaBancarizacion(float $montoTotal, string $moneda): bool
    {
        if ($moneda === 'PEN') {
            return $montoTotal > self::UMBRAL_PEN;
        }

        if ($moneda === 'USD') {
            return $montoTotal > self::UMBRAL_USD;
        }

        // Para otras monedas, no aplica por defecto
        return false;
    }

    /**
     * Obtener el umbral aplicable según moneda
     *
     * @param string $moneda
     * @return float|null
     */
    public function getUmbral(string $moneda): ?float
    {
        return match($moneda) {
            'PEN' => self::UMBRAL_PEN,
            'USD' => self::UMBRAL_USD,
            default => null,
        };
    }

    /**
     * Validar datos de bancarización
     *
     * @param array $dataBancarizacion
     * @return array ['valido' => bool, 'errores' => array]
     */
    public function validarDatosBancarizacion(array $dataBancarizacion): array
    {
        $errores = [];

        // Validar medio de pago
        if (!isset($dataBancarizacion['medio_pago'])) {
            $errores[] = 'El medio de pago es requerido para operaciones sujetas a bancarización.';
            return ['valido' => false, 'errores' => $errores];
        }

        $medioPago = MedioPagoBancarizacion::activos()
            ->byCodigo($dataBancarizacion['medio_pago'])
            ->first();

        if (!$medioPago) {
            $errores[] = 'El medio de pago especificado no es válido o no está activo.';
            return ['valido' => false, 'errores' => $errores];
        }

        // Validar campos requeridos según el medio de pago
        if ($medioPago->requiere_numero_operacion && empty($dataBancarizacion['numero_operacion'])) {
            $errores[] = "El medio de pago '{$medioPago->descripcion}' requiere número de operación.";
        }

        if ($medioPago->requiere_banco && empty($dataBancarizacion['banco'])) {
            $errores[] = "El medio de pago '{$medioPago->descripcion}' requiere especificar el banco.";
        }

        if ($medioPago->requiere_fecha && empty($dataBancarizacion['fecha_pago'])) {
            $errores[] = "El medio de pago '{$medioPago->descripcion}' requiere fecha de pago.";
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores,
            'medio_pago' => $medioPago
        ];
    }

    /**
     * Generar leyenda de bancarización para SUNAT
     *
     * @return array
     */
    public function generarLeyendaBancarizacion(): array
    {
        return [
            'code' => '2005',
            'value' => 'OPERACIÓN SUJETA A BANCARIZACIÓN - LEY N° 28194'
        ];
    }

    /**
     * Preparar datos de bancarización para almacenar en BD
     *
     * @param float $montoTotal
     * @param string $moneda
     * @param array|null $dataBancarizacion
     * @return array
     */
    public function prepararDatosBancarizacion(float $montoTotal, string $moneda, ?array $dataBancarizacion = null): array
    {
        $aplica = $this->aplicaBancarizacion($montoTotal, $moneda);
        $umbral = $this->getUmbral($moneda);

        $datos = [
            'bancarizacion_aplica' => $aplica,
            'bancarizacion_monto_umbral' => $umbral,
            'bancarizacion_validado' => false,
        ];

        if ($aplica && $dataBancarizacion) {
            $datos['bancarizacion_medio_pago'] = $dataBancarizacion['medio_pago'] ?? null;
            $datos['bancarizacion_numero_operacion'] = $dataBancarizacion['numero_operacion'] ?? null;
            $datos['bancarizacion_fecha_pago'] = $dataBancarizacion['fecha_pago'] ?? null;
            $datos['bancarizacion_banco'] = $dataBancarizacion['banco'] ?? null;
            $datos['bancarizacion_observaciones'] = $dataBancarizacion['observaciones'] ?? null;

            // Si se proporcionaron datos completos, marcar como validado
            if (!empty($datos['bancarizacion_medio_pago'])) {
                $validacion = $this->validarDatosBancarizacion($dataBancarizacion);
                $datos['bancarizacion_validado'] = $validacion['valido'];
            }
        }

        return $datos;
    }

    /**
     * Obtener todos los medios de pago activos
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMediosPagoActivos()
    {
        return MedioPagoBancarizacion::activos()->orderBy('descripcion')->get();
    }

    /**
     * Generar mensaje de advertencia cuando falta bancarización
     *
     * @param float $montoTotal
     * @param string $moneda
     * @return string
     */
    public function generarMensajeAdvertencia(float $montoTotal, string $moneda): string
    {
        $umbral = $this->getUmbral($moneda);
        $simbolo = $moneda === 'PEN' ? 'S/' : 'US$';

        return "⚠️ ADVERTENCIA LEGAL: Esta operación supera el umbral de bancarización ({$simbolo} " . number_format($umbral, 2) . "). " .
               "Según la Ley N° 28194, sin un medio de pago bancario válido, el gasto NO será deducible para Impuesto a la Renta " .
               "y NO otorgará derecho a crédito fiscal de IGV. " .
               "Se recomienda registrar el medio de pago utilizado para evitar sanciones fiscales.";
    }

    /**
     * Verificar si hay advertencias de bancarización
     *
     * @param float $montoTotal
     * @param string $moneda
     * @param array|null $dataBancarizacion
     * @return array ['tiene_advertencia' => bool, 'mensaje' => string|null]
     */
    public function verificarAdvertencias(float $montoTotal, string $moneda, ?array $dataBancarizacion = null): array
    {
        $aplica = $this->aplicaBancarizacion($montoTotal, $moneda);

        if (!$aplica) {
            return ['tiene_advertencia' => false, 'mensaje' => null];
        }

        // Si aplica pero no hay datos de bancarización
        if (!$dataBancarizacion || empty($dataBancarizacion['medio_pago'])) {
            return [
                'tiene_advertencia' => true,
                'mensaje' => $this->generarMensajeAdvertencia($montoTotal, $moneda)
            ];
        }

        // Si hay datos, validar que sean correctos
        $validacion = $this->validarDatosBancarizacion($dataBancarizacion);
        if (!$validacion['valido']) {
            return [
                'tiene_advertencia' => true,
                'mensaje' => 'Datos de bancarización incompletos: ' . implode(' ', $validacion['errores'])
            ];
        }

        return ['tiene_advertencia' => false, 'mensaje' => null];
    }

    /**
     * Log de operación con bancarización
     *
     * @param string $tipoDocumento
     * @param string $numeroDocumento
     * @param float $montoTotal
     * @param string $moneda
     * @param bool $tieneBancarizacion
     * @return void
     */
    public function logOperacion(string $tipoDocumento, string $numeroDocumento, float $montoTotal, string $moneda, bool $tieneBancarizacion): void
    {
        $aplica = $this->aplicaBancarizacion($montoTotal, $moneda);

        if ($aplica && !$tieneBancarizacion) {
            Log::warning('Documento sujeto a bancarización sin medio de pago registrado', [
                'tipo_documento' => $tipoDocumento,
                'numero_documento' => $numeroDocumento,
                'monto_total' => $montoTotal,
                'moneda' => $moneda,
                'umbral' => $this->getUmbral($moneda),
                'ley' => 'Ley N° 28194'
            ]);
        } elseif ($aplica && $tieneBancarizacion) {
            Log::info('Documento con bancarización registrada correctamente', [
                'tipo_documento' => $tipoDocumento,
                'numero_documento' => $numeroDocumento,
                'monto_total' => $montoTotal,
                'moneda' => $moneda
            ]);
        }
    }

    // ==========================================
    // NUEVOS MÉTODOS PARA MÚLTIPLES MEDIOS DE PAGO
    // ==========================================

    /**
     * Validar array de múltiples medios de pago
     *
     * @param array $mediosPago Array de medios de pago [{tipo, monto, referencia}]
     * @param float $montoTotal Monto total del documento
     * @return array ['valido' => bool, 'errores' => array, 'total_pagos' => float]
     */
    public function validarMediosPago(array $mediosPago, float $montoTotal): array
    {
        $errores = [];
        $totalPagos = 0;

        if (empty($mediosPago)) {
            return ['valido' => true, 'errores' => [], 'total_pagos' => 0];
        }

        foreach ($mediosPago as $index => $pago) {
            $posicion = $index + 1;

            // Validar que tenga tipo
            if (empty($pago['tipo'])) {
                $errores[] = "Medio de pago #{$posicion}: El tipo es requerido.";
                continue;
            }

            // Validar que el tipo exista en la tabla
            $medioPago = MedioPagoBancarizacion::activos()
                ->byCodigo($pago['tipo'])
                ->first();

            if (!$medioPago) {
                $errores[] = "Medio de pago #{$posicion}: El tipo '{$pago['tipo']}' no es válido o no está activo.";
                continue;
            }

            // Validar monto
            if (!isset($pago['monto']) || $pago['monto'] <= 0) {
                $errores[] = "Medio de pago #{$posicion} ({$medioPago->descripcion}): El monto debe ser mayor a 0.";
                continue;
            }

            // Validar referencia para medios que la requieren (excepto EFEC y POS)
            if ($medioPago->requiere_numero_operacion && empty($pago['referencia'])) {
                $errores[] = "Medio de pago #{$posicion} ({$medioPago->descripcion}): Requiere número de referencia/operación.";
            }

            $totalPagos += (float) $pago['monto'];
        }

        // Validar que la suma de pagos sea >= monto total (permite vuelto/cambio)
        if ($totalPagos > 0 && $totalPagos < $montoTotal) {
            $diferencia = $montoTotal - $totalPagos;
            $errores[] = "La suma de los medios de pago ({$totalPagos}) es menor al monto total del documento ({$montoTotal}). Falta: {$diferencia}";
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores,
            'total_pagos' => $totalPagos
        ];
    }

    /**
     * Preparar múltiples medios de pago para almacenar
     *
     * @param float $montoTotal
     * @param string $moneda
     * @param array|null $mediosPago
     * @return array|null Array procesado de medios de pago o null
     */
    public function prepararMediosPago(float $montoTotal, string $moneda, ?array $mediosPago): ?array
    {
        if (empty($mediosPago)) {
            return null;
        }

        $mediosProcesados = [];

        foreach ($mediosPago as $pago) {
            $tipo = $pago['tipo'] ?? null;
            if (!$tipo) continue;

            // Obtener descripción del medio de pago
            $medioPago = MedioPagoBancarizacion::activos()
                ->byCodigo($tipo)
                ->first();

            $mediosProcesados[] = [
                'tipo' => $tipo,
                'descripcion' => $medioPago ? $medioPago->descripcion : $tipo,
                'monto' => round((float) ($pago['monto'] ?? 0), 2),
                'referencia' => $pago['referencia'] ?? null,
            ];
        }

        return !empty($mediosProcesados) ? $mediosProcesados : null;
    }

    /**
     * Calcular total de múltiples medios de pago
     *
     * @param array|null $mediosPago
     * @return float
     */
    public function calcularTotalMediosPago(?array $mediosPago): float
    {
        if (empty($mediosPago)) {
            return 0;
        }

        return array_reduce($mediosPago, function ($total, $pago) {
            return $total + (float) ($pago['monto'] ?? 0);
        }, 0);
    }

    /**
     * Convertir formato antiguo de bancarización a múltiples medios de pago
     *
     * @param array $bancarizacion Formato antiguo: {medio_pago, numero_operacion, ...}
     * @param float $montoTotal Monto total del documento
     * @return array Formato nuevo: [{tipo, monto, referencia}]
     */
    public function convertirBancarizacionAMediosPago(array $bancarizacion, float $montoTotal): array
    {
        if (empty($bancarizacion['medio_pago'])) {
            return [];
        }

        return [
            [
                'tipo' => $bancarizacion['medio_pago'],
                'monto' => $montoTotal,
                'referencia' => $bancarizacion['numero_operacion'] ?? null,
            ]
        ];
    }

    /**
     * Obtener descripción legible de los medios de pago para mostrar en PDF
     *
     * @param array|null $mediosPago
     * @return array Array con información formateada para PDF
     */
    public function formatearMediosPagoParaPdf(?array $mediosPago): array
    {
        if (empty($mediosPago)) {
            return [];
        }

        $formateados = [];

        foreach ($mediosPago as $pago) {
            $descripcion = $pago['descripcion'] ?? $pago['tipo'];
            $referencia = $pago['referencia'] ?? '-';
            $monto = $pago['monto'] ?? 0;

            $formateados[] = [
                'tipo' => $pago['tipo'],
                'descripcion' => $descripcion,
                'monto' => $monto,
                'referencia' => $referencia,
                'texto' => "{$descripcion}: " . number_format($monto, 2) . ($referencia !== '-' ? " (Ref: {$referencia})" : ''),
            ];
        }

        return $formateados;
    }
}
