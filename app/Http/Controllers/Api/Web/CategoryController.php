<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::latest()->get();

        return new CategoryResource(true, 'List Data Category', $categories);
    }

    public function show($slug) {
        $category = Category::with('products.category') //category memanggil relasi product lalu product memanggil relasi kaategory
        ->with('products', function($query) {
            $query->withCount('reviews');
            $query->withAvg('reviews', 'rating');
        })->where('slug', $slug)->first();

        if ($category) {
            return new CategoryResource(true, 'Data Product by Category : '. $category->name. ' ', $category);
        }
        return new CategoryResource(false, 'Tidak Ada', null);
    }
}
