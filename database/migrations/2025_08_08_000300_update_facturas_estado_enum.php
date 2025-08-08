<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->enum('estado', ['borrador', 'enviado', 'pagado'])->default('borrador')->change();
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->enum('estado', ['borrador', 'emitida', 'pagada'])->default('borrador')->change();
        });
    }
};
