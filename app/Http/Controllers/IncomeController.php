<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:0|max:2147483647']);

        auth()->user()->update([
            'income' => $request->amount
        ]);

        return redirect('/dashboard');
    }

    public function update(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:0|max:2147483647']);

        auth()->user()->update([
            'income' => $request->amount
        ]);

        return back()->with('success', 'Pemasukan berhasil diperbarui!');
    }
}
