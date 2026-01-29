<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega soporte para múltiples medios de pago por documento
     * Ejemplo de estructura:
     * [
     *     { "tipo": "YAPE", "monto": 50.00, "referencia": "5321451" },
     *     { "tipo": "TRAN", "monto": 100.00, "referencia": "53413411" },
     *     { "tipo": "EFEC", "monto": 50.00, "referencia": null }
     * ]
     */
    public function up(): void
    {
        // Agregar columna medios_pago a invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->json('medios_pago')->nullable()->after('bancarizacion_observaciones')
                  ->comment('Array de medios de pago: [{tipo, monto, referencia}]');
        });

        // Agregar columna medios_pago a boletas
        Schema::table('boletas', function (Blueprint $table) {
            $table->json('medios_pago')->nullable()->after('bancarizacion_observaciones')
                  ->comment('Array de medios de pago: [{tipo, monto, referencia}]');
        });

        // Agregar columna medios_pago a credit_notes
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->json('medios_pago')->nullable()->after('datos_adicionales')
                  ->comment('Array de medios de pago: [{tipo, monto, referencia}]');
        });

        // Agregar columna medios_pago a debit_notes
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->json('medios_pago')->nullable()->after('datos_adicionales')
                  ->comment('Array de medios de pago: [{tipo, monto, referencia}]');
        });

        // Agregar EFECTIVO a medios_pago_bancarizacion si no existe
        $existeEfectivo = DB::table('medios_pago_bancarizacion')
            ->where('codigo', 'EFEC')
            ->exists();

        if (!$existeEfectivo) {
            DB::table('medios_pago_bancarizacion')->insert([
                'codigo' => 'EFEC',
                'descripcion' => 'Efectivo',
                'requiere_numero_operacion' => false,
                'requiere_banco' => false,
                'requiere_fecha' => false,
                'activo' => true,
                'observaciones' => 'Pago en efectivo - No requiere datos adicionales',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Agregar POS si no existe
        $existePos = DB::table('medios_pago_bancarizacion')
            ->where('codigo', 'POS')
            ->exists();

        if (!$existePos) {
            DB::table('medios_pago_bancarizacion')->insert([
                'codigo' => 'POS',
                'descripcion' => 'Pago con POS',
                'requiere_numero_operacion' => false, // Opcional
                'requiere_banco' => false,
                'requiere_fecha' => false,
                'activo' => true,
                'observaciones' => 'Pago mediante terminal POS - Referencia opcional',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Agregar YAPE si no existe
        $existeYape = DB::table('medios_pago_bancarizacion')
            ->where('codigo', 'YAPE')
            ->exists();

        if (!$existeYape) {
            DB::table('medios_pago_bancarizacion')->insert([
                'codigo' => 'YAPE',
                'descripcion' => 'Yape (BCP)',
                'requiere_numero_operacion' => true,
                'requiere_banco' => false,
                'requiere_fecha' => false,
                'activo' => true,
                'observaciones' => 'Billetera digital de BCP - Requiere código de operación',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Agregar PLIN si no existe
        $existePlin = DB::table('medios_pago_bancarizacion')
            ->where('codigo', 'PLIN')
            ->exists();

        if (!$existePlin) {
            DB::table('medios_pago_bancarizacion')->insert([
                'codigo' => 'PLIN',
                'descripcion' => 'Plin',
                'requiere_numero_operacion' => true,
                'requiere_banco' => false,
                'requiere_fecha' => false,
                'activo' => true,
                'observaciones' => 'Billetera digital interbancaria - Requiere código de operación',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('medios_pago');
        });

        Schema::table('boletas', function (Blueprint $table) {
            $table->dropColumn('medios_pago');
        });

        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropColumn('medios_pago');
        });

        Schema::table('debit_notes', function (Blueprint $table) {
            $table->dropColumn('medios_pago');
        });

        // Eliminar los medios de pago agregados por esta migración
        DB::table('medios_pago_bancarizacion')
            ->whereIn('codigo', ['EFEC', 'POS', 'YAPE', 'PLIN'])
            ->delete();
    }
};
