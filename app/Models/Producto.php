<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $primaryKey = 'id_producto';
    public $timestamps    = false;

    protected $fillable = [
        'id_negocio',
        'id_categoria',
        'nombre',
        'descripcion',
        'precio',
        'disponible',
        'stock',
        'stock_minimo',
    ];

    protected function casts(): array
    {
        return [
            'precio'      => 'decimal:2',
            'disponible'  => 'boolean',
            'stock'       => 'integer',
            'stock_minimo'=> 'integer',
        ];
    }

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function itemsPedido(): HasMany
    {
        return $this->hasMany(ItemPedido::class, 'id_producto', 'id_producto');
    }

    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }
}
