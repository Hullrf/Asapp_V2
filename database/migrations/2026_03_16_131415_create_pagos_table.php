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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago');
            $table->foreignId('id_pedido')
                  ->nullable()
                  ->constrained('pedidos', 'id_pedido');
            $table->foreignId('id_item')
                  ->nullable()
                  ->constrained('items_pedido', 'id_item')
                  ->nullOnDelete();
            $table->decimal('monto', 10, 2)->nullable();
            $table->enum('metodo_pago', ['digital', 'efectivo'])->default('digital');
            $table->enum('estado', ['exitoso', 'fallido', 'simulado'])->default('simulado');
            $table->timestamp('fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
