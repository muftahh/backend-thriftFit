<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index() {
        $sliders = Slider::latest()->get();

        return new SliderResource(true, 'List Data Slider', $sliders);
    }
}
