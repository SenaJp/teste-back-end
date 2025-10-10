<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

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

        $products = $query->orderByDesc('id')->paginate(15);
        return response()->json($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $product = Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'image_url' => $validated['image_url'] ?? null,
        ]);

        if (!empty($validated['categories']) && is_array($validated['categories'])) {
            $product->categories()->attach($validated['categories']);
        }

        $product->load('categories');
        return response()->json($product, 201);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load('categories');
        return response()->json($product);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();
        $product->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'image_url' => $validated['image_url'] ?? null,
        ]);

        if (array_key_exists('categories', $validated) && is_array($validated['categories'] ?? null)) {
            $product->categories()->sync($validated['categories']);
        }

        $product->load('categories');
        return response()->json($product);
    }

    public function destroy(Product $product): JsonResponse
    {
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
