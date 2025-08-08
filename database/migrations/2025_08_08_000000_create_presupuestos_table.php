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
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->date('fecha');
            $table->integer('numero');
            $table->string('serie')->default('PRES');
            $table->string('estado')->default('pendiente'); // pendiente, aceptado, rechazado
            $table->unsignedInteger('validez_dias')->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedTinyInteger('activo')->default(1);
            $table->decimal('iva_porcentaje', 5, 2)->default(21.00);
            $table->decimal('base_imponible', 14, 2);
            $table->decimal('iva_total', 14, 2)->default(0);
            $table->decimal('irpf_total', 14, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->timestamps();
            $table->unique(['usuario_id','serie','numero']);
            $table->unique(['serie', 'numero']);
            $table->index(['usuario_id','cliente_id','estado','fecha', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
