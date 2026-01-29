<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega campo descuento_global a facturas y boletas.
 * Este campo almacena el monto total de descuentos globales aplicados al documento.
 * Los descuentos globales son diferentes de los descuentos por línea (por ítem).
 */
return new class extends Migration
{
    public function up(): void
    {
        $tables = ['invoices', 'boletas', 'credit_notes', 'debit_notes'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'descuento_global')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('descuento_global', 12, 2)->default(0)->after('mto_descuentos');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['invoices', 'boletas', 'credit_notes', 'debit_notes'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'descuento_global')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('descuento_global');
                });
            }
        }
    }
};
