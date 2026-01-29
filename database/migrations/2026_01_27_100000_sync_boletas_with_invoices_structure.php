<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sincroniza la estructura de la tabla boletas con invoices.
 * Ambas tablas deben tener la misma estructura ya que envían los mismos datos a SUNAT.
 * La única diferencia es:
 * - tipo_documento: 01 (Factura) vs 03 (Boleta)
 * - Método de envío: Individual (Facturas) vs Resumen Diario (Boletas)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boletas', function (Blueprint $table) {
            // Fecha de vencimiento (para crédito)
            if (!Schema::hasColumn('boletas', 'fecha_vencimiento')) {
                $table->date('fecha_vencimiento')->nullable()->after('fecha_emision');
            }

            // Forma de pago
            if (!Schema::hasColumn('boletas', 'forma_pago_tipo')) {
                $table->string('forma_pago_tipo', 20)->default('Contado')->after('moneda');
            }
            if (!Schema::hasColumn('boletas', 'forma_pago_cuotas')) {
                $table->json('forma_pago_cuotas')->nullable()->after('forma_pago_tipo');
            }

            // Montos faltantes
            if (!Schema::hasColumn('boletas', 'mto_oper_exportacion')) {
                $table->decimal('mto_oper_exportacion', 12, 2)->default(0)->after('mto_oper_inafectas');
            }
            if (!Schema::hasColumn('boletas', 'mto_otros_tributos')) {
                $table->decimal('mto_otros_tributos', 12, 2)->default(0)->after('mto_icbper');
            }
            if (!Schema::hasColumn('boletas', 'mto_detraccion')) {
                $table->decimal('mto_detraccion', 12, 2)->default(0)->after('mto_otros_tributos');
            }
            if (!Schema::hasColumn('boletas', 'mto_percepcion')) {
                $table->decimal('mto_percepcion', 12, 2)->default(0)->after('mto_detraccion');
            }
            if (!Schema::hasColumn('boletas', 'mto_retencion')) {
                $table->decimal('mto_retencion', 12, 2)->default(0)->after('mto_percepcion');
            }
            if (!Schema::hasColumn('boletas', 'mto_anticipos')) {
                $table->decimal('mto_anticipos', 12, 2)->default(0)->after('mto_imp_venta');
            }

            // Documentos relacionados
            if (!Schema::hasColumn('boletas', 'guias')) {
                $table->json('guias')->nullable()->after('leyendas');
            }
            if (!Schema::hasColumn('boletas', 'documentos_relacionados')) {
                $table->json('documentos_relacionados')->nullable()->after('guias');
            }
            if (!Schema::hasColumn('boletas', 'detraccion')) {
                $table->json('detraccion')->nullable()->after('documentos_relacionados');
            }
            if (!Schema::hasColumn('boletas', 'percepcion')) {
                $table->json('percepcion')->nullable()->after('detraccion');
            }
            if (!Schema::hasColumn('boletas', 'retencion')) {
                $table->json('retencion')->nullable()->after('percepcion');
            }

            // Consulta CPE (igual que facturas)
            if (!Schema::hasColumn('boletas', 'consulta_cpe_estado')) {
                $table->string('consulta_cpe_estado', 50)->nullable()->after('codigo_hash');
            }
            if (!Schema::hasColumn('boletas', 'consulta_cpe_respuesta')) {
                $table->json('consulta_cpe_respuesta')->nullable()->after('consulta_cpe_estado');
            }
            if (!Schema::hasColumn('boletas', 'consulta_cpe_fecha')) {
                $table->timestamp('consulta_cpe_fecha')->nullable()->after('consulta_cpe_respuesta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('boletas', function (Blueprint $table) {
            $columnsToRemove = [
                'fecha_vencimiento',
                'forma_pago_tipo',
                'forma_pago_cuotas',
                'mto_oper_exportacion',
                'mto_otros_tributos',
                'mto_detraccion',
                'mto_percepcion',
                'mto_retencion',
                'mto_anticipos',
                'guias',
                'documentos_relacionados',
                'detraccion',
                'percepcion',
                'retencion',
                'consulta_cpe_estado',
                'consulta_cpe_respuesta',
                'consulta_cpe_fecha',
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('boletas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
