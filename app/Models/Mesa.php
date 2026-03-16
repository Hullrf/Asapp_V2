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
        'mesa_principal_id',
    ];

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function mesaPrincipal(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_principal_id', 'id_mesa');
    }

    public function mesasUnidas(): HasMany
    {
        return $this->hasMany(Mesa::class, 'mesa_principal_id', 'id_mesa');
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

    public function estaUnida(): bool
    {
        return $this->mesa_principal_id !== null;
    }

    public function estaOcupada(): bool
    {
        if ($this->estaUnida()) {
            // Carga la relación si no está cargada aún
            $principal = $this->mesaPrincipal ?? $this->mesaPrincipal()->first();
            return $principal?->pedidoActivo()->exists() ?? false;
        }

        return $this->pedidoActivo()->exists();
    }
}
