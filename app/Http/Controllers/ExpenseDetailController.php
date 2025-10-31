<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ExpenseDetail; 
use App\Models\Expense;

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

        // Hitung ulang total untuk parent
        $total = ExpenseDetail::where('expense_id', $request->note_id)
            ->selectRaw('SUM(qty * price) as total')
            ->value('total');

        Expense::where('id', $request->note_id)
            ->update(['amount' => $total]);

        $details = ExpenseDetail::where('expense_id', $request->note_id)->get();
        return response()->json(['details' => $details, 'total' => $total]);
    }

    // Hapus detail
    public function destroy($id)
    {
        $detail = ExpenseDetail::findOrFail($id);
        $expense_id = $detail->expense_id;
        $detail->delete();

        $total = ExpenseDetail::where('expense_id', $expense_id)
            ->selectRaw('SUM(qty * price) as total')
            ->value('total') ?? 0;

        Expense::where('id', $expense_id)
            ->update(['amount' => $total]);

        $details = ExpenseDetail::where('expense_id', $expense_id)->get();

        return response()->json(['details' => $details, 'total' => $total]);
    }

}
