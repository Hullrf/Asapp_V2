<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // pedidos: cargar panel/mesero y verificar mesa activa
        Schema::table('pedidos', function (Blueprint $table) {
            $table->index(['id_mesa', 'estado'],    'idx_pedidos_mesa_estado');
            $table->index(['id_negocio', 'estado'], 'idx_pedidos_negocio_estado');
        });

        // items_pedido: pagos con lockForUpdate filtran por pedido + estado
        Schema::table('items_pedido', function (Blueprint $table) {
            $table->index(['id_pedido', 'estado'], 'idx_items_pedido_estado');
        });

        // productos: mesero lista disponibles por negocio
        Schema::table('productos', function (Blueprint $table) {
            $table->index(['id_negocio', 'disponible'], 'idx_productos_negocio_disponible');
        });

        // users: cargar meseros por negocio
        Schema::table('users', function (Blueprint $table) {
            $table->index(['id_negocio', 'rol'], 'idx_users_negocio_rol');
        });

        // mesas: renumeración al eliminar mesa de un piso
        Schema::table('mesas', function (Blueprint $table) {
            $table->index(['id_piso', 'numero'], 'idx_mesas_piso_numero');
        });

        // division_partes: búsqueda por token dentro de una división
        Schema::table('division_partes', function (Blueprint $table) {
            $table->index(['id_division', 'estado'],             'idx_division_partes_division_estado');
            $table->index(['id_division', 'participante_token'], 'idx_division_partes_division_token');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropIndex('idx_pedidos_mesa_estado');
            $table->dropIndex('idx_pedidos_negocio_estado');
        });

        Schema::table('items_pedido', function (Blueprint $table) {
            $table->dropIndex('idx_items_pedido_estado');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex('idx_productos_negocio_disponible');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_negocio_rol');
        });

        Schema::table('mesas', function (Blueprint $table) {
            $table->dropIndex('idx_mesas_piso_numero');
        });

        Schema::table('division_partes', function (Blueprint $table) {
            $table->dropIndex('idx_division_partes_division_estado');
            $table->dropIndex('idx_division_partes_division_token');
        });
    }
};
