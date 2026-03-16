<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('productos', function (Blueprint $table) {
            $table->unsignedInteger('stock')->nullable()->after('disponible');
            $table->unsignedInteger('stock_minimo')->default(5)->after('stock');
            $table->foreignId('id_categoria')->nullable()->after('id_negocio')
                  ->constrained('categorias', 'id_categoria')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['id_categoria']);
            $table->dropColumn(['stock', 'stock_minimo', 'id_categoria']);
        });
    }
};
