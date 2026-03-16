<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $primaryKey = 'id_pago';
    public $timestamps    = false;

    protected $fillable = [
        'id_pedido',
        'id_item',
        'monto',
        'metodo_pago',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemPedido::class, 'id_item', 'id_item');
    }
}
