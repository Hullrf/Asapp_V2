<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemDivision extends Model
{
    protected $table      = 'item_divisiones';
    protected $primaryKey = 'id_division';
    public $timestamps    = false;

    protected $fillable = ['id_item', 'total_partes', 'iniciador_token', 'estado'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemPedido::class, 'id_item', 'id_item');
    }

    public function partes(): HasMany
    {
        return $this->hasMany(DivisionParte::class, 'id_division', 'id_division');
    }
}
