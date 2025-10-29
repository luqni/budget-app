<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ExpenseDetail; 

class ExpenseDetailController extends Controller
{
    // Ambil semua detail untuk 1 catatan
    public function index($id)
    {
        $details = ExpenseDetail::where('expense_id', $id)->get();
        return response()->json($details);
    }

    // Tambah detail
    public function store(Request $request)
    {
        $detail = ExpenseDetail::create([
            'expense_id' => $request->note_id,
            'name'       => $request->name,
            'qty'        => $request->qty,
            'price'      => $request->price,
        ]);

        // return list terbaru
        $details = ExpenseDetail::where('expense_id', $request->note_id)->get();
        return response()->json(['details' => $details]);
    }

    // Hapus detail
    public function destroy($id)
    {
        $detail = ExpenseDetail::findOrFail($id);
        $expense_id = $detail->expense_id;
        $detail->delete();

        $details = ExpenseDetail::where('expense_id', $expense_id)->get();
        return response()->json(['details' => $details]);
    }
}
