<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('series')) {
            Schema::create('series', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
                $table->enum('tipo', ['presupuesto','pedido','factura']);
                $table->string('serie', 20)->default('A');
                $table->unsignedBigInteger('siguiente_numero')->default(1);
                $table->timestamps();
                $table->unique(['usuario_id','tipo','serie']);
            });
        }
    }
    public function down(): void { Schema::dropIfExists('series'); }
};
