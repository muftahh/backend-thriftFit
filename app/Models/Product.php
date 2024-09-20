<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'image', 'title', 'slug', 'category_id', 'user_id', 'weight', 'price', 'stock', 'discount'
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    /**
     * Accessor : untuk mengubah output dari image
     * domain.com/storage/campaigns/nama_file_image.png
     */
    protected function image() : Attribute {
        return Attribute::make(
            get: fn ($value) => url('/storage/products/'. $value),
        );
    }

    protected function reviewAvgRating() : Attribute {
        return Attribute::make(
            get: fn ($value) => $value ? substr($value, 0, 3) : 0,
        );
    }
}
