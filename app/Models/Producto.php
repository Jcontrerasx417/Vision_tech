<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    public const ESTADOS = ['pendiente_revision', 'aprobado', 'rechazado', 'activo', 'inactivo'];

    protected $fillable = ['proveedor_id', 'categoria_producto_id', 'nombre', 'descripcion', 'imagen_url', 'precio', 'peso_kg', 'stock', 'activo', 'estado', 'motivo_rechazo', 'reportado'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'reportado' => 'boolean'];
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function pedidoItems()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_producto_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class)->orderBy('orden');
    }

    public function imagenPrincipal()
    {
        return $this->hasOne(ProductoImagen::class)->where('principal', true);
    }

    public function estaPublicado(): bool
    {
        return $this->activo && in_array($this->estado, ['aprobado', 'activo'], true);
    }

    public function imagenPrincipalUrl(): string
    {
        return $this->imagen_url
            ?: ($this->relationLoaded('imagenes') ? $this->imagenes->first()?->url : $this->imagenes()->value('url'))
            ?: 'https://images.unsplash.com/photo-1526406915894-7bcd65f60845?auto=format&fit=crop&w=900&q=80';
    }
}
