<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presupuestos', function (Blueprint $table) {
            $table->renameColumn('observaciones', 'notas');
            $table->dropColumn('iva_porcentaje');
        });

        Schema::table('presupuestos', function (Blueprint $table) {
            $table->enum('estado', ['borrador', 'enviado', 'aceptado', 'rechazado'])->default('borrador')->change();
        });

        Schema::table('presupuestos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->foreign('cliente_id')->references('id')->on('clientes')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('presupuestos', function (Blueprint $table) {
            $table->renameColumn('notas', 'observaciones');
            $table->decimal('iva_porcentaje', 5, 2)->default(21.00);
        });

        Schema::table('presupuestos', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'aceptado', 'rechazado'])->default('pendiente')->change();
        });

        Schema::table('presupuestos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->foreign('cliente_id')->references('id')->on('clientes')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
