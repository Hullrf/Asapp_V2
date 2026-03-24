<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_negocio', function (Blueprint $table) {
            $table->foreignId('id_user')
                  ->constrained('users', 'id_usuario')
                  ->cascadeOnDelete();
            $table->foreignId('id_negocio')
                  ->constrained('negocios', 'id_negocio')
                  ->cascadeOnDelete();
            $table->primary(['id_user', 'id_negocio']);
        });

        // Migrar relaciones existentes al pivot
        DB::table('users')
            ->whereNotNull('id_negocio')
            ->get()
            ->each(fn($u) => DB::table('user_negocio')->insertOrIgnore([
                'id_user'    => $u->id_usuario,
                'id_negocio' => $u->id_negocio,
            ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('user_negocio');
    }
};
