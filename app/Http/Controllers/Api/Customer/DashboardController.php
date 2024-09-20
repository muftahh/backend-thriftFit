<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\Invoice;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index() {
        $pending = Invoice::where('statur', 'pending')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();
        $success = Invoice::where('statur', 'success')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();
        $expired = Invoice::where('statur', 'expired')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();
        $failed  = Invoice::where('statur', 'failed')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();
    
        return response()->json([
            'success' => true,
            'message' => 'Statistik Data',  
            'data'    => [
                'count' => [
                    'pending'   => $pending,
                    'success'   => $success,
                    'expired'   => $expired,
                    'failed'    => $failed
                ]
            ]  
        ], 200);
    }

}
