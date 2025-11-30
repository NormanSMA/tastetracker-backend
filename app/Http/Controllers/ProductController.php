<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // GET /api/products (Público)
    public function index()
    {
        // Traemos productos con su categoría para optimizar
        $products = Product::with('category')->where('is_active', true)->get();
        // Nota: Si es admin, quizás querría ver los inactivos también,
        // pero por ahora mostraremos los activos por defecto.

        return ProductResource::collection($products);
    }

    // POST /api/products
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return new ProductResource($product);
    }

    // GET /api/products/{id}
    public function show(Product $product)
    {
        return new ProductResource($product->load('category'));
    }

    // PUT /api/products/{id}
    public function update(StoreProductRequest $request, Product $product)
    {
        $data = $request->validated();
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('image')) {
            try {
                // Validar existencia lógica y física antes de borrar
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            } catch (\Exception $e) {
                // Loguear el error pero NO detener la actualización del resto de datos
                \Log::error('Error actualizando imagen de producto ' . $product->id . ': ' . $e->getMessage());
                // Opcional: podrías retornar error si la imagen es crítica,
                // pero para UX es mejor guardar el resto y avisar.
            }
        }

        $product->update($data);

        return new ProductResource($product->load('category'));
    }

    // DELETE /api/products/{id}
    public function destroy(Product $product)
    {
        // Al usar SoftDeletes, no borramos la imagen del disco inmediatamente
        // para permitir restaurar el producto después si fue un error.
        $product->delete();

        return response()->json(['message' => 'Producto eliminado (Soft Delete)']);
    }
}
