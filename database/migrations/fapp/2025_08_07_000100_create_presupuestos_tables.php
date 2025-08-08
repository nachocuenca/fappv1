<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('presupuestos')) {
            Schema::create('presupuestos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnUpdate()->restrictOnDelete();
                $table->date('fecha')->nullable();
                $table->unsignedBigInteger('numero')->default(0);
                $table->string('serie', 20)->default('A');
                $table->enum('estado', ['borrador','enviado','aceptado','rechazado'])->default('borrador');
                $table->unsignedInteger('validez_dias')->nullable();
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
        if (!Schema::hasTable('presupuesto_productos')) {
            Schema::create('presupuesto_productos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('presupuesto_id')->constrained('presupuestos')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('producto_id')->nullable()->constrained('productos')->cascadeOnUpdate()->nullOnDelete();
                $table->string('descripcion');
                $table->decimal('cantidad', 12, 3)->default(1);
                $table->decimal('precio_unitario', 12, 2)->default(0);
                $table->decimal('iva_porcentaje', 5, 2)->default(21);
                $table->decimal('irpf_porcentaje', 5, 2)->nullable();
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->timestamps();
                $table->index(['presupuesto_id']);
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('presupuesto_productos');
        Schema::dropIfExists('presupuestos');
    }
};
