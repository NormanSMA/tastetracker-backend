<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // GET /api/categories (Público para ver el menú)
    public function index()
    {
        // Retornamos solo las activas al público general, o todas si es admin (lógica simple por ahora: todas)
        return CategoryResource::collection(Category::all());
    }

    // POST /api/categories
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($data);

        return new CategoryResource($category);
    }

    // GET /api/categories/{id}
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    // PUT /api/categories/{id}
    public function update(StoreCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('image')) {
            // Borrar imagen vieja si existe
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return new CategoryResource($category);
    }

    // DELETE /api/categories/{id}
    public function destroy(Category $category)
    {
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json(['message' => 'Categoría eliminada']);
    }
}
