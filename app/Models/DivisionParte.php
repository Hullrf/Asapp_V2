<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DivisionParte extends Model
{
    protected $table      = 'division_partes';
    protected $primaryKey = 'id_parte';
    public $timestamps    = false;

    protected $fillable = ['id_division', 'numero_parte', 'monto', 'participante_token', 'estado'];

    public function division(): BelongsTo
    {
        return $this->belongsTo(ItemDivision::class, 'id_division', 'id_division');
    }
}
