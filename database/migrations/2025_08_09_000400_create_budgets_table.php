<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nombre');
            $table->decimal('monto', 14, 2);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->index(['usuario_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
