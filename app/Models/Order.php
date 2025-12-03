<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'waiter_id', 'area_id', 'table_number', 
        'status', 'order_type', 'total', 'notes', 'guest_name'
    ];

    protected $with = ['area']; // Siempre cargar área para table_identifier

    protected $appends = ['table_identifier']; // Añadir a JSON automáticamente

    // Accessor: Identificador de mesa con prefijo (ej: "S3", "T5")
    public function getTableIdentifierAttribute()
    {
        if ($this->area && $this->table_number) {
            return $this->area->prefix . $this->table_number;
        }
        return $this->table_number ? 'Mesa ' . $this->table_number : 'N/A';
    }

    // Cliente que hizo el pedido
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Mesero que atendió
    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    // Área (Terraza, Salón, etc.)
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    // Los platillos del pedido
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
