<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id('id_categoria');
            $table->foreignId('id_negocio')->constrained('negocios', 'id_negocio')->cascadeOnDelete();
            $table->string('nombre', 80);
        });
    }
    public function down(): void { Schema::dropIfExists('categorias'); }
};
