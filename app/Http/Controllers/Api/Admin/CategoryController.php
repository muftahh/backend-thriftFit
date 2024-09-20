<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::when(request()->q, function($categories) {
            $categories = $categories->where('name', 'like', '%'. request()->q .'%');
        })->latest()->paginate(5);

        return new CategoryResource(true, 'List Data Categories', $categories);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'image'    => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'name'     => 'required|unique:categories',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        $category = Category::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-')
        ]);
        if ($category) {
            return new CategoryResource(true, 'berhasil menambakan category', $category);
        }
        return new CategoryResource(false, 'gagal menambakan category', null);
    }

    public function show($id) {
        $category = Category::whereId($id)->first();

        if ($category) {
            return new CategoryResource(true, 'berhasil detail category', $category);
        }
        return new CategoryResource(false, 'gagal detail category', null);
    }

    public function update(Request $request, Category $category) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,'.$category->id,
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            Storage::disk('local')->delete('public/categories/'.basename($category->image));

            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            $category->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
            ]);
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            return new CategoryResource(true, 'Berhasil Update Category', $category);
        }
        return new CategoryResource(false, 'Gagal Update Category', null);
    }

    public function destroy(Category $category) {
        Storage::disk('local')->delete('public/categories/'.basename($category->image));

        if ($category->delete()) {
            return new CategoryResource(true, 'Berhasil menghapus Category', null);
        }
        return new CategoryResource(false, 'Gagal menghapus Category', null);
    }
}
