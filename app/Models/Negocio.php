<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Negocio extends Model
{
    protected $primaryKey = 'id_negocio';
    public $timestamps    = false;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'suspendido',
        'config_panel',
    ];

    protected function casts(): array
    {
        return [
            'config_panel' => 'array',
        ];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'id_negocio', 'id_negocio');
    }

    /** Usuarios asociados vía pivot (incluye sedes creadas con "Nueva sede"). */
    public function administradores(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_negocio', 'id_negocio', 'id_user');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'id_negocio', 'id_negocio');
    }

    public function mesas(): HasMany
    {
        return $this->hasMany(Mesa::class, 'id_negocio', 'id_negocio');
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'id_negocio', 'id_negocio');
    }

    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class, 'id_negocio', 'id_negocio');
    }

    public function pisos(): HasMany
    {
        return $this->hasMany(Piso::class, 'id_negocio', 'id_negocio');
    }
}
