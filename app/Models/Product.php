<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'image_url',
        'external_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    public function scopeWithImage($query)
    {
        return $query->whereNotNull('image_url');
    }

    public function scopeWithoutImage($query)
    {
        return $query->whereNull('image_url');
    }

    public function scopeByNameAndCategory($query, $name, $categoryId)
    {
        return $query->where('name', 'like', "%{$name}%")
                    ->whereHas('categories', function ($q) use ($categoryId) {
                        $q->where('categories.id', $categoryId);
                    });
    }
}
