<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ImportController extends Controller
{
    public function importAll(): JsonResponse
    {
        try {
            $response = Http::get('https://fakestoreapi.com/products');

            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to fetch products from API'], 500);
            }

            $products = $response->json();
            $imported = 0;
            $skipped = 0;

            foreach ($products as $productData) {
                if ($this->createProduct($productData)) {
                    $imported++;
                } else {
                    $skipped++;
                }
            }

            return response()->json([
                'message' => 'Import completed successfully',
                'imported' => $imported,
                'skipped' => $skipped
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error importing products: ' . $e->getMessage()], 500);
        }
    }

    public function importSpecific($id): JsonResponse
    {
        try {
            $response = Http::get("https://fakestoreapi.com/products/{$id}");

            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to fetch product from API'], 404);
            }

            $productData = $response->json();
            $product = $this->createProduct($productData);

            if ($product) {
                return response()->json([
                    'message' => 'Product imported successfully',
                    'product' => $product
                ]);
            } else {
                return response()->json(['error' => 'Product already exists'], 409);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error importing product: ' . $e->getMessage()], 500);
        }
    }

    private function createProduct($productData)
    {
        try {
            $existingProduct = Product::withTrashed()->where('external_id', $productData['id'])->first();

            if ($existingProduct) {
                if ($existingProduct->trashed()) {
                    $existingProduct->restore();
                    return $existingProduct;
                }
                return false;
            }

            $category = Category::firstOrCreate(
                ['name' => $productData['category']],
                ['description' => "Category imported from FakeStore API"]
            );

            $product = Product::create([
                'name' => $productData['title'],
                'price' => $productData['price'],
                'description' => $productData['description'],
                'image_url' => $productData['image'],
                'external_id' => $productData['id']
            ]);

            $product->categories()->attach($category->id);
            $product->load('categories');

            return $product;

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
