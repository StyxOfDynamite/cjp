<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use Illuminate\Http\Request;

class PublicMotorcycleController extends Controller
{
    public function home()
    {
        $bikes = Motorcycle::with(['images' => fn($q) => $q->where('is_primary', true)])
            ->where('stock_quantity', '>', 0)
            ->orderByDesc('is_featured')
            ->orderBy('stock_quantity')
            ->orderBy('make')
            ->limit(12)
            ->get();

        $inStockCount = Motorcycle::where('stock_quantity', '>', 0)->count();

        return view('home', compact('bikes', 'inStockCount'));
    }

    public function index(Request $request)
    {
        $filters = $request->only(['make', 'model', 'year_min', 'year_max', 'price_max', 'in_stock']);

        $motorcycles = Motorcycle::with(['images' => fn($q) => $q->where('is_primary', true)])
            ->filter($filters)
            ->orderByDesc('is_featured')
            ->orderBy('stock_quantity')
            ->orderBy('make')
            ->paginate(12)
            ->withQueryString();

        $makes = Motorcycle::distinct()->orderBy('make')->pluck('make');

        return view('motorcycles.index', compact('motorcycles', 'filters', 'makes'));
    }

    public function show(Motorcycle $motorcycle)
    {
        $motorcycle->load('images');

        return view('motorcycles.show', compact('motorcycle'));
    }
}
