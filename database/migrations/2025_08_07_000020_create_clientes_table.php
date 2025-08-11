<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('clientes')) {
            Schema::create('clientes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')
                    ->constrained('users')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                $table->string('nombre');
                $table->string('cif')->nullable();
                $table->string('email')->nullable();
                $table->string('telefono')->nullable();
                $table->string('direccion')->nullable();

                $table->timestamps();

                $table->index(['usuario_id', 'nombre']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
