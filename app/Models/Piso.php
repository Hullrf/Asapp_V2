<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Piso extends Model
{
    protected $primaryKey = 'id_piso';
    public $timestamps    = false;

    protected $fillable = ['id_negocio', 'nombre', 'orden'];

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function mesas(): HasMany
    {
        return $this->hasMany(Mesa::class, 'id_piso', 'id_piso');
    }
}
