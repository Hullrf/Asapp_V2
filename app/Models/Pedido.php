<?php

namespace App\Models;

use App\Enums\EstadoPedido;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $primaryKey = 'id_pedido';
    public $timestamps    = false;

    protected $fillable = [
        'id_negocio',
        'id_mesa',
        'id_mesero',
        'codigo_qr',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoPedido::class,
        ];
    }

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'id_mesa', 'id_mesa');
    }

    public function mesero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_mesero', 'id_usuario');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemPedido::class, 'id_pedido', 'id_pedido');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'id_pedido', 'id_pedido');
    }

    public function estaPagado(): bool
    {
        return $this->items()->where('estado', 'Pendiente')->doesntExist();
    }

    public function totalPendiente(): float
    {
        return (float) $this->items()->where('estado', 'Pendiente')->sum('subtotal');
    }
}
