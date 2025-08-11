<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('actuaciones')) {
            Schema::create('actuaciones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnUpdate()->restrictOnDelete();
                $table->string('codigo')->nullable();
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_fin')->nullable();
                $table->enum('estado', ['abierta','en_proceso','completada'])->default('abierta');
                $table->text('notas')->nullable();
                $table->timestamps();
                $table->index(['usuario_id','cliente_id','estado','fecha_inicio']);
            });
        }

        if (!Schema::hasTable('pedidos')) {
            Schema::create('pedidos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignId('presupuesto_id')->nullable()->constrained('presupuestos')->cascadeOnUpdate()->nullOnDelete();
                $table->foreignId('actuacion_id')->nullable()->constrained('actuaciones')->cascadeOnUpdate()->nullOnDelete();
                $table->date('fecha')->nullable();
                $table->unsignedBigInteger('numero')->default(0);
                $table->string('serie', 20)->default('A');
                $table->enum('estado', ['borrador','confirmado','servido','cerrado'])->default('borrador');
                $table->text('notas')->nullable();
                $table->decimal('base_imponible', 14, 2)->default(0);
                $table->decimal('iva_total', 14, 2)->default(0);
                $table->decimal('irpf_total', 14, 2)->default(0);
                $table->decimal('total', 14, 2)->default(0);
                $table->timestamps();
                $table->unique(['usuario_id','serie','numero']);
                $table->index(['usuario_id','cliente_id','estado','fecha']);
            });
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
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('actuaciones');
    }
};
