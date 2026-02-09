<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Debt;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get Debts (Hutang) - I owe money
        $payables = Debt::where('user_id', $user->id)
            ->where('type', 'payable')
            ->orderBy('status', 'desc') // 'unpaid' > 'paid', so desc puts unpaid first
            ->orderBy('due_date', 'asc')
            ->get();

        // Get Receivables (Piutang) - People owe me money
        $receivables = Debt::where('user_id', $user->id)
            ->where('type', 'receivable')
            ->orderBy('status', 'desc') // 'unpaid' > 'paid'
            ->orderBy('due_date', 'asc')
            ->get();

        $totalPayable = $payables->where('status', 'unpaid')->sum('amount');
        $totalReceivable = $receivables->where('status', 'unpaid')->sum('amount');

        return view('debts.index', compact('payables', 'receivables', 'totalPayable', 'totalReceivable'));
    }

    public function store(Request $request)
    {
        // Remove dots from amount
        $request->merge(['amount' => str_replace('.', '', $request->amount)]);

        // Remove dots from amount
        $request->merge(['amount' => str_replace('.', '', $request->amount)]);

        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:payable,receivable',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        Debt::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'amount' => $request->amount,
            'type' => $request->type,
            'due_date' => $request->due_date,
            'description' => $request->description,
            'status' => 'unpaid'
        ]);

        return redirect()->route('debts.index')->with('success', 'Berhasil menambahkan data!');
    }

    public function update(Request $request, $id)
    {
        $debt = Debt::where('user_id', Auth::id())->findOrFail($id);

        // Remove dots from amount
        $request->merge(['amount' => str_replace('.', '', $request->amount)]);

        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $debt->update($request->only(['name', 'amount', 'due_date', 'description']));

        return redirect()->route('debts.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $debt = Debt::where('user_id', Auth::id())->findOrFail($id);
        $debt->delete();

        return redirect()->route('debts.index')->with('success', 'Data berhasil dihapus!');
    }

    public function markAsPaid($id)
    {
        $debt = Debt::where('user_id', Auth::id())->findOrFail($id);
        
        $debt->status = ($debt->status === 'paid') ? 'unpaid' : 'paid';
        $debt->save();

        $message = ($debt->status === 'paid') ? 'Ditandai sebagai lunas!' : 'Ditandai sebagai belum lunas.';
        
        return redirect()->back()->with('success', $message);
    }
}
