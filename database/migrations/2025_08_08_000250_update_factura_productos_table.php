<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('factura_productos', function (Blueprint $table) {
            // remove unique constraint to allow repeated lines
            try {
                $table->dropUnique('factura_productos_factura_id_producto_id_unique');
            } catch (\Exception $e) {
                // index might not exist in fresh installations
            }
        });

        Schema::table('factura_productos', function (Blueprint $table) {
            // allow producto_id to be nullable and support fractional quantities
            $table->foreignId('producto_id')->nullable()->change();
            $table->decimal('cantidad', 12, 3)->default(1)->change();

            // remove obsolete columns
            if (Schema::hasColumn('factura_productos', 'iva')) {
                $table->dropColumn('iva');
            }
            if (Schema::hasColumn('factura_productos', 'total_linea')) {
                $table->dropColumn('total_linea');
            }

            // add new tax and subtotal columns if missing
            if (!Schema::hasColumn('factura_productos', 'iva_porcentaje')) {
                $table->decimal('iva_porcentaje', 5, 2)->default(21);
            }
            if (!Schema::hasColumn('factura_productos', 'irpf_porcentaje')) {
                $table->decimal('irpf_porcentaje', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('factura_productos', 'subtotal')) {
                $table->decimal('subtotal', 14, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('factura_productos', function (Blueprint $table) {
            $table->dropColumn(['iva_porcentaje', 'irpf_porcentaje', 'subtotal']);
            $table->decimal('iva', 5, 2)->default(21);
            $table->decimal('total_linea', 12, 2)->default(0);
            $table->unsignedBigInteger('producto_id')->nullable(false)->change();
            $table->integer('cantidad')->default(1)->change();
            $table->unique(['factura_id', 'producto_id']);
        });
    }
};

