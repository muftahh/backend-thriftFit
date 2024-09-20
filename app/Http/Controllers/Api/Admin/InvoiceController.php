<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Invoice;
use App\Http\Controllers\Controller;
use App\Http\Resources\IncoiceResource;

class InvoiceController extends Controller
{
    public function index() {
        $invoices = Invoice::with('customer')->when(request()->q, function($invoices) {
            $invoices = $invoices->where('invoice', 'like', '%'. request()->q .'%');
        })->latest()->paginate(5);

        return new IncoiceResource(true, 'List Data Invoice', $invoices);
    }

    public function show($id) {
        $invoice = Invoice::whit('orders.product', 'customer', 'city', 'province')->whereId($id)->first();

        if ($invoice) {
            return new IncoiceResource(true, 'Berhasil Menampilkan Invoice', $invoice);
        }
        return new IncoiceResource(false, 'Gagal Menampilkan Invoice', null);

    }
}
