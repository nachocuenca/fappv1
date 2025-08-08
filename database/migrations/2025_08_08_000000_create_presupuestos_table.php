<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
