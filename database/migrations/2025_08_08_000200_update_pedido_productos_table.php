<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pedido_productos', function (Blueprint $table) {
            // remove unique constraint to allow repeated lines
            $table->dropUnique('pedido_productos_pedido_id_producto_id_unique');
        });

        Schema::table('pedido_productos', function (Blueprint $table) {
            // allow producto_id to be nullable and support fractional quantities
            $table->foreignId('producto_id')->nullable()->change();
            $table->decimal('cantidad', 12, 3)->default(1)->change();

            // add new descriptive and tax columns
            $table->string('descripcion', 255);
            $table->decimal('iva_porcentaje', 5, 2)->default(21);
            $table->decimal('irpf_porcentaje', 5, 2)->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('pedido_productos', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'iva_porcentaje', 'irpf_porcentaje', 'subtotal']);
            $table->unsignedBigInteger('producto_id')->nullable(false)->change();
            $table->decimal('cantidad', 12, 2)->default(1)->change();
            $table->unique(['pedido_id', 'producto_id']);
        });
    }
};

