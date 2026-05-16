<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function update(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
        $request->validate(['stock_quantity' => 'required|integer|min:0']);
        $motorcycle->update(['stock_quantity' => $request->stock_quantity]);

        return back()->with('success', 'Stock updated.');
    }
}
