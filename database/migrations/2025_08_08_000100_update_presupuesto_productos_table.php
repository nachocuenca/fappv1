<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('presupuesto_productos', function (Blueprint $table) {
            // remove unique constraint to allow repeated lines
            $table->dropUnique('presupuesto_productos_presupuesto_id_producto_id_unique');
        });

        Schema::table('presupuesto_productos', function (Blueprint $table) {
            // allow producto_id to be nullable
            $table->foreignId('producto_id')->nullable()->change();

            // add new descriptive and tax columns
            $table->string('descripcion', 255);
            $table->decimal('iva_porcentaje', 5, 2)->default(21);
            $table->decimal('irpf_porcentaje', 5, 2)->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('presupuesto_productos', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'iva_porcentaje', 'irpf_porcentaje', 'subtotal']);
            $table->unsignedBigInteger('producto_id')->nullable(false)->change();
            $table->unique(['presupuesto_id', 'producto_id']);
        });
    }
};

