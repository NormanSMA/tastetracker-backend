<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'prefix', 'total_tables', 'description', 'is_active'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
