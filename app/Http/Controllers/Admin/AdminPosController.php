<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;

class AdminPosController extends Controller
{
    public function receipt(Sale $sale)
    {
        return view('admin.pos.receipt', [
            'sale' => $sale->load('items', 'customer', 'cashier'),
        ]);
    }
}
