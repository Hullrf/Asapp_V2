<?php

namespace App\Models;

use App\Enums\RolUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_usuario';
    public $timestamps    = false;

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol',
        'id_negocio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'rol'      => RolUsuario::class,
        ];
    }

    /** Negocio principal (FK directo, para compatibilidad). */
    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    /** Todas las sedes a las que tiene acceso el usuario. */
    public function negocios(): BelongsToMany
    {
        return $this->belongsToMany(
            Negocio::class,
            'user_negocio',
            'id_user',
            'id_negocio'
        );
    }

    /**
     * Sede actualmente seleccionada (almacenada en sesión).
     * Si la sesión está vacía o apunta a una sede sin acceso, usa la sede principal.
     */
    public function negocioActivo(): Negocio
    {
        $idActivo = session('sede_activa_id');

        if ($idActivo) {
            $negocio = $this->negocios()->find($idActivo);
            if ($negocio) return $negocio;
        }

        // Fallback: sede principal
        session(['sede_activa_id' => $this->id_negocio]);
        return $this->negocio;
    }

    /** ID de la sede activa (shorthand para comparaciones de autorización). */
    public function idNegocioActivo(): int
    {
        return $this->negocioActivo()->id_negocio;
    }

    public function esAdmin(): bool
    {
        return $this->rol === RolUsuario::Admin;
    }
}
