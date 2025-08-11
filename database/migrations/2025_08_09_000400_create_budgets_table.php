<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('budgets')) {
            Schema::create('budgets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnUpdate()->restrictOnDelete();
                $table->date('fecha')->nullable();
                $table->unsignedBigInteger('numero')->default(0);
                $table->string('serie', 20)->default('A');
                $table->enum('estado', ['borrador','enviado','aceptado','rechazado'])->default('borrador');
                $table->unsignedInteger('validez_dias')->nullable();
                $table->text('notas')->nullable();
                $table->boolean('activo')->default(true);
                $table->decimal('base_imponible', 14, 2)->default(0);
                $table->decimal('iva_total', 14, 2)->default(0);
                $table->decimal('irpf_total', 14, 2)->default(0);
                $table->decimal('total', 14, 2)->default(0);
                $table->timestamps();
                $table->unique(['usuario_id','serie','numero']);
                $table->index(['usuario_id','cliente_id','estado','fecha','activo']);
            });
        }
        if (!Schema::hasTable('budget_products')) {
            Schema::create('budget_products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('budget_id')->constrained('budgets')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('producto_id')->nullable()->constrained('productos')->cascadeOnUpdate()->nullOnDelete();
                $table->string('descripcion');
                $table->decimal('cantidad', 12, 3)->default(1);
                $table->decimal('precio_unitario', 12, 2)->default(0);
                $table->decimal('iva_porcentaje', 5, 2)->default(21);
                $table->decimal('irpf_porcentaje', 5, 2)->nullable();
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->timestamps();
                $table->index(['budget_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_products');
        Schema::dropIfExists('budgets');
    }
};
