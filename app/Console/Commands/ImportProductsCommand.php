<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import {--id= : Import a specific product by external ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from FakeStore API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $externalId = $this->option('id');

        if ($externalId) {
            $this->importSingleProduct($externalId);
        } else {
            $this->importAllProducts();
        }
    }

    /**
     * Import a single product by external ID
     */
    private function importSingleProduct($externalId)
    {
        $this->info("Importing product with ID: {$externalId}");

        try {
            $response = Http::get("https://fakestoreapi.com/products/{$externalId}");

            if ($response->successful()) {
                $productData = $response->json();
                $this->createProduct($productData);
                $this->info("Product imported successfully!");
            } else {
                $this->error("Failed to fetch product with ID: {$externalId}");
            }
        } catch (\Exception $e) {
            $this->error("Error importing product: " . $e->getMessage());
            Log::error("Import product error: " . $e->getMessage());
        }
    }

    /**
     * Import all products from the API
     */
    private function importAllProducts()
    {
        $this->info("Importing all products from FakeStore API...");

        try {
            $response = Http::get('https://fakestoreapi.com/products');

            if ($response->successful()) {
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

                $this->info("Import completed! Imported: {$imported}, Skipped: {$skipped}");
            } else {
                $this->error("Failed to fetch products from API");
            }
        } catch (\Exception $e) {
            $this->error("Error importing products: " . $e->getMessage());
            Log::error("Import products error: " . $e->getMessage());
        }
    }

    /**
     * Create a product from API data
     */
    private function createProduct($productData)
    {
        try {
            $existingProduct = Product::where('external_id', $productData['id'])->first();

            if ($existingProduct) {
                $this->warn("Product with external ID {$productData['id']} already exists. Skipping...");
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

            $this->info("Created product: {$product->name}");
            return true;

        } catch (\Exception $e) {
            $this->error("Error creating product: " . $e->getMessage());
            Log::error("Create product error: " . $e->getMessage());
            return false;
        }
    }
}
