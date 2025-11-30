<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price, // Asegurar formato numérico
            'category_id' => $this->category_id,
            'category_name' => $this->category->name, // Eager loading access
            'image_url' => $this->image ? url(Storage::url($this->image)) : null,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
