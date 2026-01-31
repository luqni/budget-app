<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category', 'all');
        
        $query = Quote::query()->orderBy('created_at', 'desc');
        
        if ($category !== 'all') {
            $query->where('category', $category);
        }
        
        $quotes = $query->paginate(20);
        
        $categoryCounts = [
            'all' => Quote::count(),
            'quran' => Quote::where('category', 'quran')->count(),
            'hadits' => Quote::where('category', 'hadits')->count(),
            'ulama' => Quote::where('category', 'ulama')->count(),
            'tips' => Quote::where('category', 'tips')->count(),
        ];
        
        return view('quotes.index', compact('quotes', 'category', 'categoryCounts'));
    }
}
