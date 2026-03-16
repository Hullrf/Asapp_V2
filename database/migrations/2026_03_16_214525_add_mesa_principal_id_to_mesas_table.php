<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->unsignedBigInteger('mesa_principal_id')
                  ->nullable()
                  ->after('codigo_qr');

            $table->foreign('mesa_principal_id')
                  ->references('id_mesa')
                  ->on('mesas')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropForeign(['mesa_principal_id']);
            $table->dropColumn('mesa_principal_id');
        });
    }
};
