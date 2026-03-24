<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pisos', function (Blueprint $table) {
            $table->id('id_piso');
            $table->foreignId('id_negocio')->constrained('negocios', 'id_negocio')->cascadeOnDelete();
            $table->string('nombre', 50);
            $table->unsignedSmallInteger('orden')->default(0);
        });

        Schema::table('mesas', function (Blueprint $table) {
            $table->foreignId('id_piso')
                  ->nullable()
                  ->after('id_negocio')
                  ->constrained('pisos', 'id_piso')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropForeign(['id_piso']);
            $table->dropColumn('id_piso');
        });

        Schema::dropIfExists('pisos');
    }
};
