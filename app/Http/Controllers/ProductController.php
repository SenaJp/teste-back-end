<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('categories');

        if ($request->has('name') && $request->name) {
            $query->byName($request->name);
        }

        if ($request->has('category_id') && $request->category_id) {
            $query->byCategory($request->category_id);
        }

        if ($request->has('name') && $request->has('category_id') && $request->name && $request->category_id) {
            $query = Product::byNameAndCategory($request->name, $request->category_id);
        }

        if ($request->has('with_image') && $request->with_image === 'true') {
            $query->withImage();
        }

        if ($request->has('without_image') && $request->without_image === 'true') {
            $query->withoutImage();
        }

        $products = $query->paginate(15);
        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'image_url' => 'nullable|url',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'image_url' => $request->image_url
        ]);

        if ($request->has('categories') && is_array($request->categories)) {
            $product->categories()->attach($request->categories);
        }

        $product->load('categories');
        return response()->json($product, 201);
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::with('categories')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'image_url' => 'nullable|url',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id'
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'image_url' => $request->image_url
        ]);

        if ($request->has('categories') && is_array($request->categories)) {
            $product->categories()->sync($request->categories);
        }

        $product->load('categories');
        return response()->json($product);
    }

    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function byCategory(Request $request, string $categoryId): JsonResponse
    {
        $products = Product::byCategory($categoryId)->with('categories')->get();
        return response()->json($products);
    }

    public function withImage(): JsonResponse
    {
        $products = Product::withImage()->with('categories')->get();
        return response()->json($products);
    }

    public function withoutImage(): JsonResponse
    {
        $products = Product::withoutImage()->with('categories')->get();
        return response()->json($products);
    }
}
