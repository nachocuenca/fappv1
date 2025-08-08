<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->string('serie')->default('PRES');
            $table->integer('numero');
            $table->date('fecha');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->decimal('base_imponible', 10, 2);
            $table->decimal('iva', 5, 2)->default(21);
            $table->decimal('total', 10, 2);
            $table->string('estado')->default('pendiente'); // pendiente, aceptado, rechazado
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
