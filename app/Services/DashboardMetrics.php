<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class DashboardMetrics
{
    /**
     * Compute dashboard metrics safely, guarding against missing tables/columns during migrations.
     *
     * @return array{
     *   totalProducts:int,
     *   totalCategories:int,
     *   productsWithImage:int,
     *   productsWithoutImage:int,
     *   recentProducts: \Illuminate\Support\Collection
     * }
     */
    public function get(): array
    {
        $totalProducts = 0;
        $totalCategories = 0;
        $productsWithImage = 0;
        $productsWithoutImage = 0;
        $recentProducts = collect();

        $hasProducts = Schema::hasTable('products');
        $hasCategories = Schema::hasTable('categories');
        $hasDeletedAt = $hasProducts && Schema::hasColumn('products', 'deleted_at');

        if ($hasProducts && $hasCategories && $hasDeletedAt) {
            $totalProducts = Product::count();
            $totalCategories = Category::count();
            $productsWithImage = Product::whereNotNull('image_url')->count();
            $productsWithoutImage = Product::whereNull('image_url')->count();
            $recentProducts = Product::with('categories')->latest()->take(5)->get();
        }

        return compact('totalProducts', 'totalCategories', 'productsWithImage', 'productsWithoutImage', 'recentProducts');
    }
}
