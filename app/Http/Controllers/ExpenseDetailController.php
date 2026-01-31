<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ExpenseDetail; 
use App\Models\Expense;

use Illuminate\Support\Facades\Log;

class ExpenseDetailController extends Controller
{
    // Ambil semua detail untuk 1 catatan
    public function index($id)
    {
        Log::info("Fetching details for Expense ID: $id");
        $details = ExpenseDetail::where('expense_id', $id)->get();
        Log::info("Found " . $details->count() . " details.");
        return response()->json($details);
    }

    // Tambah detail
    // Tambah detail
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'note_id' => 'required|exists:expenses,id',
                'name' => 'required|string|max:255',
                'qty' => 'required|integer|min:1|max:2147483647',
                'price' => 'required|numeric|min:0|max:2147483647',
            ]);

            $detail = ExpenseDetail::create([
                'expense_id' => $request->note_id,
                'name'       => $request->name,
                'qty'        => $request->qty,
                'price'      => $request->price,
                'is_checked' => false, // Explicitly set default
            ]);

            // Hitung ulang total untuk parent
            $total = ExpenseDetail::where('expense_id', $request->note_id)
                ->selectRaw('SUM(qty * price) as total')
                ->value('total') ?? 0;

            Expense::where('id', $request->note_id)
                ->update(['amount' => $total]);

            $details = ExpenseDetail::where('expense_id', $request->note_id)->get();
            return response()->json(['success' => true, 'details' => $details, 'total' => $total]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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

    public function check(Request $request, $id)
    {
        $detail = ExpenseDetail::findOrFail($id);
        $detail->update(['is_checked' => $request->is_checked]);

        // Hitung ulang total pengeluaran pada parent expense
        $total = ExpenseDetail::where('expense_id', $detail->expense_id)
            ->where('is_checked', true)
            ->selectRaw('SUM(qty * price) as total')
            ->value('total') ?? 0;

        // Update kolom amount pada expense
        // $detail->expense->update(['amount' => $total]);

        return response()->json([
            'success' => true,
            'total' => $total
        ]);
    }


}
