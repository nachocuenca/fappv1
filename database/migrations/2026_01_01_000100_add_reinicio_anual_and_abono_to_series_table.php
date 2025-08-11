<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('series', function (Blueprint $table) {
            $table->boolean('reinicio_anual')->default(false);
        });

        DB::statement("ALTER TABLE series MODIFY tipo ENUM('presupuesto','pedido','factura','abono')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE series MODIFY tipo ENUM('presupuesto','pedido','factura')");

        Schema::table('series', function (Blueprint $table) {
            $table->dropColumn('reinicio_anual');
        });
    }
};
