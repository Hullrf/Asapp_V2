<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    protected $primaryKey = 'id_mesa';
    public $timestamps    = false;

    protected $fillable = [
        'id_negocio',
        'nombre',
        'codigo_qr',
    ];

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'id_mesa', 'id_mesa');
    }

    public function pedidoActivo(): HasMany
    {
        return $this->hasMany(Pedido::class, 'id_mesa', 'id_mesa')
                    ->whereIn('estado', ['Pendiente', 'Parcial']);
    }

    public function estaOcupada(): bool
    {
        return $this->pedidoActivo()->exists();
    }
}
