<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Actualizar enum en users para incluir mesero
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN rol ENUM('admin','cliente','mesero') NOT NULL DEFAULT 'admin'");
        }

        // Agregar id_mesero a pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('id_mesero')
                  ->nullable()
                  ->after('id_mesa')
                  ->constrained('users', 'id_usuario')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['id_mesero']);
            $table->dropColumn('id_mesero');
        });
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN rol ENUM('admin','cliente') NOT NULL DEFAULT 'admin'");
        }
    }
};
