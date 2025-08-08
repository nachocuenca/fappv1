<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('presupuesto_id')->nullable()->constrained('presupuestos')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('actuacion_id')->nullable()->constrained('actuaciones')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedBigInteger('numero')->default(0);
            $table->string('serie', 20)->default('A');
            $table->date('fecha');
            $table->enum('estado', ['borrador', 'emitida', 'pagada'])->default('borrador');
            $table->text('notas')->nullable();
            $table->decimal('base_imponible', 14, 2)->default(0);
            $table->decimal('iva_total', 14, 2)->default(0);
            $table->decimal('irpf_total', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamps();
            $table->unique(['usuario_id','serie','numero']);
        });

        Schema::create('factura_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('producto_id')->nullable()->constrained('productos')->onDelete('set null')->onUpdate('cascade');
            $table->string('descripcion');
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('iva', 5, 2)->default(21.00); // porcentaje
            $table->decimal('total_linea', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_productos');
        Schema::dropIfExists('facturas');
    }
};
