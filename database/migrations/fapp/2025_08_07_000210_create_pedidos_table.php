<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('presupuestos')) {
            throw new RuntimeException('La tabla presupuestos debe existir antes de crear pedidos');
        }
        if (!Schema::hasTable('actuaciones')) {
            throw new RuntimeException('La tabla actuaciones debe existir antes de crear pedidos');
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
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
