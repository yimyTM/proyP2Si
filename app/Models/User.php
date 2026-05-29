<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table      = 'users';
    protected $primaryKey = 'idUsuario';

    protected $fillable = [
        'nombreCompleto',
        'telefono',
        'correo',
        'password',
        'idRol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // ─── Relaciones ────────────────────────────────────────────────────────────

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'idRol', 'idRol');
    }

    public function docente(): HasOne
    {
        return $this->hasOne(Docente::class, 'idUsuario', 'idUsuario');
    }

    public function postulante(): HasOne
    {
        return $this->hasOne(Postulante::class, 'idUsuario', 'idUsuario');
    }

    public function bitacoras(): HasMany
    {
        return $this->hasMany(Bitacora::class, 'idUsuario', 'idUsuario');
    }

    // ─── Helpers de rol ────────────────────────────────────────────────────────

    public function hasRole(string $nombreRol): bool
    {
        return $this->rol?->nombre_Rol === $nombreRol;
    }

    public function esAdmin(): bool
    {
        return $this->hasRole('Administrador');
    }

    public function esDocente(): bool
    {
        return $this->hasRole('Docente');
    }

    public function esPostulante(): bool
    {
        return $this->hasRole('Postulante');
    }

    /** Laravel usa "email" en password_reset_tokens; aquí devolvemos el correo del usuario. */
    public function getEmailForPasswordReset(): string
    {
        return $this->correo;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
