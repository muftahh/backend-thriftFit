<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index() {
        $pending = Invoice::where('statur', 'pending')->count();
        $success = Invoice::where('statur', 'success')->count();
        $expired = Invoice::where('statur', 'expired')->count();
        $failed  = Invoice::where('statur', 'failed')->count();

        $year = date('Y');

        // cart
        $transactions = DB::table('invoices')
            ->addSelect(DB::raw('SUM(grand_total) as grand_total'))
            ->addSelect(DB::raw('MONTH(created_at) as month'))
            ->addSelect(DB::raw('MONTHNAME(created_at) as month_name'))
            ->addSelect(DB::raw('YEAR(created_at) as year'))
            ->whereYear('created_at', '=', $year)
            ->where('statur', 'success')
            ->groupBy('month')
            ->orderByRaw('month ASC')
            ->get();

        if (count($transactions)) {
            foreach ($transactions as $result) {
                $month_name[]    = $result->month_name;
                $grand_total[]   = (int)$result->grand_total;
            }
        } else {
            $month_name[]   = "";
            $grand_total[]  = "";
        }

        return response()->json([
            'success' => true,
            'message' => 'Statistik Data',  
            'data'    => [
                'count' => [
                    'pending'   => $pending,
                    'success'   => $success,
                    'expired'   => $expired,
                    'failed'    => $failed
                ],
                'chart' => [
                    'month_name'    => $month_name,
                    'grand_total'   => $grand_total
                ]
            ]  
        ], 200);
    }
}
