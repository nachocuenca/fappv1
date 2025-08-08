<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('actuacion_productos')) {
            Schema::create('actuacion_productos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('actuacion_id')->constrained('actuaciones')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('producto_id')->nullable()->constrained('productos')->cascadeOnUpdate()->nullOnDelete();
                $table->string('descripcion');
                $table->decimal('cantidad', 12, 3)->default(1);
                $table->decimal('precio_unitario', 12, 2)->default(0);
                $table->decimal('iva_porcentaje', 5, 2)->default(21);
                $table->decimal('irpf_porcentaje', 5, 2)->nullable();
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->timestamps();
                $table->index(['actuacion_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('actuacion_productos');
    }
};

