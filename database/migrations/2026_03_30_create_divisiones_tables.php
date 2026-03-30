<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_divisiones', function (Blueprint $table) {
            $table->id('id_division');
            $table->unsignedBigInteger('id_item');
            $table->foreign('id_item')->references('id_item')->on('items_pedido')->cascadeOnDelete();
            $table->unsignedTinyInteger('total_partes');
            $table->string('iniciador_token', 64);
            $table->enum('estado', ['pendiente', 'cancelada'])->default('pendiente');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('division_partes', function (Blueprint $table) {
            $table->id('id_parte');
            $table->foreignId('id_division')->constrained('item_divisiones', 'id_division')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero_parte');
            $table->decimal('monto', 10, 2);
            $table->string('participante_token', 64)->nullable();
            $table->enum('estado', ['libre', 'tomada'])->default('libre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('division_partes');
        Schema::dropIfExists('item_divisiones');
    }
};
