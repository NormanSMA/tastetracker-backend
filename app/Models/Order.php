<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'waiter_id', 'area_id', 'table_number', 
        'status', 'order_type', 'total', 'notes'
    ];

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
