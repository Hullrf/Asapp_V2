<?php

namespace App\Models;

use App\Enums\EstadoItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemPedido extends Model
{
    protected $table      = 'items_pedido';
    protected $primaryKey = 'id_item';
    public $timestamps    = false;

    protected $fillable = [
        'id_pedido',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'precio_unitario' => 'decimal:2',
            'subtotal'        => 'decimal:2',
            'estado'          => EstadoItem::class,
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'id_item', 'id_item');
    }

    public function estaPendiente(): bool
    {
        return $this->estado === EstadoItem::Pendiente;
    }

    public function divisionActiva(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\ItemDivision::class, 'id_item', 'id_item')
                    ->where('estado', 'pendiente');
    }

    public function divisiones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ItemDivision::class, 'id_item', 'id_item');
    }
}
