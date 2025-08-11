<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('productos')) {
            Schema::create('productos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->decimal('precio', 12, 2)->default(0);
                $table->decimal('iva_porcentaje', 5, 2)->default(21);
                $table->boolean('activo')->default(true);
                $table->timestamps();
                $table->index(['usuario_id','activo']);
            });
        }
    }
    public function down(): void { Schema::dropIfExists('productos'); }
};
