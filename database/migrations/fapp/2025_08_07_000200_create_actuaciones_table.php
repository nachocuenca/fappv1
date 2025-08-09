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
    }

    public function down(): void
    {
        Schema::dropIfExists('actuaciones');
    }
};
