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
    public function update(Request $request, Product $product)
    {
        // 1. Datos básicos manuales (sin imagen aún)
        $data = [
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
        ];

        // 2. Generar slug si hay nombre
        if ($request->has('name')) {
            $data['slug'] = Str::slug($request->input('name'));
        }

        // 3. Manejar is_active si viene en el request
        if ($request->has('is_active')) {
            $data['is_active'] = $request->input('is_active', true);
        }

        // 4. Manejo explícito de la imagen
        if ($request->hasFile('image')) {
            // Si hay imagen NUEVA, borramos la vieja y subimos la nueva
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        // NOTA: Si no hay archivo, NO tocamos la key 'image' en $data.

        // 5. Actualizar
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
