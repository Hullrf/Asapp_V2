<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pagos MODIFY COLUMN metodo_pago ENUM('digital','efectivo','tarjeta','pse','nequi') NOT NULL DEFAULT 'digital'");
    }

    public function down(): void
    {
        // Revertir registros con valores nuevos antes de reducir el enum
        DB::statement("UPDATE pagos SET metodo_pago = 'digital' WHERE metodo_pago IN ('tarjeta','pse','nequi')");
        DB::statement("ALTER TABLE pagos MODIFY COLUMN metodo_pago ENUM('digital','efectivo') NOT NULL DEFAULT 'digital'");
    }
};
