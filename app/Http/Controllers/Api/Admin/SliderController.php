<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use Illuminate\Support\Facades\Storage; //untuk uploadgambar
use Illuminate\Support\Facades\Validator; //untuk validasi

class SliderController extends Controller
{
    public function index() {
        $sliders = Slider::latest()->paginate(5);
        
        return new SliderResource(true, 'List Data Sliders', $sliders);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000'
        ]);
        if ($validator->fails()) {
            return request()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        $slider = Slider::create([
            'image' => $image->hashName(),
            'link' => $request->link,
        ]);
        if ($slider) {
            return new SliderResource(true, 'Berhasil Menambahkan Slider', $slider);
        }
        return new SliderResource(false, 'Gagal Menambahkan Slider', null);
    }

    public function destroy(Slider $slider) {
        Storage::disk('local')->delete('public/sliders/'.basename($slider->image));
    
        if ($slider->delete()) {
            return new SliderResource(true, 'Berhasil Menghapus Slider', null);
        }
        return new SliderResource(false, 'Berhasil Menghapus Slider', null);
    }
}
