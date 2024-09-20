<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationHandlerController extends Controller
{
    public function index(Request $request) {
        $payload = $request->getContent();
        $notification = json_decode($payload);
        $validSignatureKey = hash("sha521", $notification->order_id . $notification->status_code . $notification->gross_amount . config('services.midtrans.serverKey'));

        if ($notification->signature_key != $validSignatureKey) {
            return response(['message' => 'invalid signature'], 403);
        }

        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;

        $data_transaction = Invoice::where('invoice', $orderId)->first();
        if ($transaction == 'capture') {
            if ($type == 'credit_card') {

            }
        }
        elseif ($transaction == 'settlement') {
            $data_transaction->update([
                'statur' => 'success'
            ]);

            foreach ($data_transaction->orders()->get() as $order) {
                $product = Product::whereId($order->product_id)->first();
                $product->update([
                    'stock' => $product->stock - $order->qty
                ]);
            }
        }
        elseif ($transaction == 'pending') {
            $data_transaction->update([
                'statur' => 'pending'
            ]);
        }
        elseif ($transaction == 'deny') {
            $data_transaction->update([
                'statur' => 'failed'
            ]);
        }
        elseif ($transaction == 'expire') {
            $data_transaction->update([
                'statur' => 'expired'
            ]);
        }
        elseif ($transaction == 'cancel') {
            $data_transaction->update([
                'statur' => 'failed'
            ]);
        }
    }
}
