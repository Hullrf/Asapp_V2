<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->unsignedSmallInteger('numero')->nullable()->after('id_piso');
            $table->string('alias', 50)->nullable()->after('nombre');
        });

        // Para cada piso existente, numerar mesas secuencialmente por id_mesa.
        // Si el nombre actual difiere de "Mesa N", se guarda como alias automáticamente.
        DB::table('pisos')->get()->each(function ($piso) {
            DB::table('mesas')
                ->where('id_piso', $piso->id_piso)
                ->orderBy('id_mesa')
                ->get()
                ->each(function ($mesa, $index) {
                    $numero   = $index + 1;
                    $canonico = 'Mesa ' . $numero;
                    $alias    = ($mesa->nombre !== $canonico) ? $mesa->nombre : null;

                    DB::table('mesas')
                        ->where('id_mesa', $mesa->id_mesa)
                        ->update([
                            'numero' => $numero,
                            'nombre' => $canonico,
                            'alias'  => $alias,
                        ]);
                });
        });
    }

    public function down(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropColumn(['numero', 'alias']);
        });
    }
};
