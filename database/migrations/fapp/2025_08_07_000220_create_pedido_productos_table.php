<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('pedidos')) {
            throw new RuntimeException('La tabla pedidos debe existir antes de crear pedido_productos');
        }
        if (!Schema::hasTable('productos')) {
            throw new RuntimeException('La tabla productos debe existir antes de crear pedido_productos');
        }
        if (!Schema::hasTable('pedido_productos')) {
            Schema::create('pedido_productos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('producto_id')->nullable()->constrained('productos')->cascadeOnUpdate()->nullOnDelete();
                $table->string('descripcion');
                $table->decimal('cantidad', 12, 3)->default(1);
                $table->decimal('precio_unitario', 12, 2)->default(0);
                $table->decimal('iva_porcentaje', 5, 2)->default(21);
                $table->decimal('irpf_porcentaje', 5, 2)->nullable();
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->timestamps();
                $table->index(['pedido_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_productos');
    }
};
