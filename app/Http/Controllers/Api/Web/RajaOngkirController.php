<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\RajaOngkirResource;

class RajaOngkirController extends Controller
{
    public function getProvinces() {
        $provinces = Province::all();

        return new RajaOngkirResource(true, 'List Data Provinces', $provinces);
    }

    public function getCities(Request $request) {
        $province = Province::where('province_id', $request->province_id)->first();
        $cities = City::where('province_id', $request->province_id)->get();

        return new RajaOngkirResource(true, 'List Data by ' . $province->name . ' ', $cities);
    }

    public function checkOngkir(Request $request) {
        $response = Http::withHeaders([
            'key' => config('services.rajaongkir.key')
        ])->post('https://api.rajaongkir.com/starter/cost', [
            'origin' => 444, //id asal pengiriman
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier
        ]);

        return new RajaOngkirResource(true, 'List Biaya Ongkir : ' . $request->courier . '', $response['rajaongkir']['results'][0]['costs']);
    }
}
