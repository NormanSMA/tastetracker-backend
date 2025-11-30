<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'table_number' => $this->table_number,
            'status' => $this->status,
            'order_type' => $this->order_type,
            'total' => (float) $this->total,
            'customer' => $this->customer ? $this->customer->name : 'Cliente General',
            'waiter' => $this->waiter->name,
            'area' => $this->area ? $this->area->name : 'N/A',
            'items' => OrderItemResource::collection($this->items), // Anidamos los items
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
