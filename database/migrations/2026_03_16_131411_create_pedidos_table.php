<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id('id_pedido');
            $table->foreignId('id_negocio')
                  ->constrained('negocios', 'id_negocio');
            $table->foreignId('id_mesa')
                  ->nullable()
                  ->constrained('mesas', 'id_mesa')
                  ->nullOnDelete();
            $table->string('codigo_qr', 100)->nullable()->unique();
            $table->enum('estado', ['Pendiente', 'Parcial', 'Pagado'])->default('Pendiente');
            $table->timestamp('fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
