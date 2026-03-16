<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'id_negocio', 'id_negocio');
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
}
