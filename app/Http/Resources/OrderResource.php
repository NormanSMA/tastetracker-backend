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
            'formatted_id' => 'Pedido #' . str_pad($this->id, 4, '0', STR_PAD_LEFT), // Ej: Pedido #0012
            'table_number' => $this->table_number,
            'status' => $this->status,
            'order_type' => $this->order_type,
            'total' => (float) $this->total,
            'formatted_total' => 'C$ ' . number_format($this->total, 2),
            'customer' => $this->customer ? $this->customer->name : 'Cliente General',
            'customer_name' => $this->guest_name ?? ($this->customer ? $this->customer->name : 'Cliente Casual'),
            'guest_name' => $this->guest_name,
            'waiter' => $this->waiter->name,
            'waiter_name' => $this->waiter ? $this->waiter->name : 'Sin Asignar',
            'area' => $this->area ? $this->area->name : 'N/A',
            'area_name' => $this->area ? $this->area->name : 'General', // Para mostrar "Terraza"
            'items' => OrderItemResource::collection($this->whenLoaded('items')), // Anidamos los items
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('d/m/Y h:i A'),
            'created_date' => $this->created_at->format('Y-m-d'), // Para agrupar por día
            'created_time' => $this->created_at->format('h:i A'), // Hora legible (12:30 PM)
            'human_date' => ucfirst($this->created_at->locale('es')->isoFormat('dddd D [de] MMMM')), // Ej: Martes 2 de Diciembre
        ];
    }
}
