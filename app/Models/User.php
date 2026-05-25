<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'telefono', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function carrito()
    {
        return $this->hasOne(Carrito::class);
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }

    public function proveedor()
    {
        return $this->hasOne(Proveedor::class);
    }

    public function proveedorPerfil()
    {
        return $this->hasOne(ProveedorPerfil::class);
    }

    public function solicitudesProveedor()
    {
        return $this->hasMany(SolicitudProveedor::class);
    }

    public function direcciones()
    {
        return $this->hasMany(DireccionCliente::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
