<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('actuacion_factura')) {
            Schema::create('actuacion_factura', function (Blueprint $table) {
                $table->id();
                $table->foreignId('actuacion_id')->constrained('actuaciones')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('factura_id')->constrained('facturas')->cascadeOnUpdate()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['actuacion_id']);
                $table->index(['factura_id']);
            });
        }
    }
    public function down(): void { Schema::dropIfExists('actuacion_factura'); }
};
