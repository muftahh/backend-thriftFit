<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index() {
        $products = Product::with('category')->when(request()->q, function($products) {
            $products = $products->where('title', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);
        
        return new ProductResource(true, 'List Data Product', $products);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title' => 'required|unique:products',
            'category_id' => 'required',
            'desc' => 'required',
            'weight' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'discount' => 'required'
        ]);
        if ($validator->fails()) {
            return request()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        $product = Product::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id' => Auth()->guard('api_admin')->user()->id,
            'desc' => $request->desc,
            'weight' => $request->weight,
            'price' => $request->price,
            'stock' => $request->stock,
            'discount' => $request->discount
        ]);
        if ($product) {
            return new ProductResource(true, 'Berhasil Menambahkan Product', $product);
        }
        return new ProductResource(false, 'Gagal Menambahkan Product', null);
    }

    public function show($id) {
        $product = Product::whereId($id)->first();

        if ($product) {
            return new ProductResource(true, 'Berhasil Menampilkan Product', $product);
        }
        return new ProductResource(false, 'Gagal Menampilkan Product', null);
    }

    public function update(Request $request, Product $product) {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:products,title,'.$product->id, //bersifat unik dan tdk boleh ada yang sama didalam tabel produk, khusus id akan dikecualikan
            'category_id'   => 'required',
            'desc'   => 'required',
            'weight'        => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'discount'      => 'required'
        ]);
        if ($validator->fails()) {
            return request()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            Storage::disk('local')->delete('public/products'.basename($product->image));

            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            $product->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'slug' => Str::slug($request->title, '-'),
                'category_id' => $request->category_id,
                'user_id' => Auth()->guard('api_admin')->user()->id,
                'desc' => $request->desc,
                'weight' => $request->weight,
                'price' => $request->price,
                'stock' => $request->stock,
                'discount' => $request->discount
            ]);
        }

        $product->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id' => Auth()->guard('api_admin')->user()->id,
            'desc' => $request->desc,
            'weight' => $request->weight,
            'price' => $request->price,
            'stock' => $request->stock,
            'discount' => $request->discount
        ]);

        if ($product) {
            return new ProductResource(true, 'Berhasil Mengupdate Product', $product);
        }
        return new ProductResource(false, 'Gagal Mengupdate Product', null);
    }

    public function destroy(Product $product) {
        Storage::disk('local')->delete('public/products'.basename($product->image));

        if ($product->delete()) {
            return new ProductResource(true, 'Berhasil Menghapus Product', null);
        }
        return new ProductResource(false, 'Gagal Menghapus Product', null);
    
    }
}
