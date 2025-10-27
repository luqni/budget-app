<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;

class DashboardController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest()->get();
        $totalExpense = $expenses->sum('amount');

        // Misal sementara income statis
        $income = 5000000;
        $expense = $totalExpense;

        return view('dashboard', compact('expenses', 'income', 'expense', 'totalExpense'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'note' => 'required|string',
            'amount' => 'required|numeric|min:1'
        ]);

        $expense = Expense::create([
            'note' => $request->note,
            'amount' => $request->amount
        ]);

        return response()->json($expense);
    }

    public function chartData()
    {
        $driver = \DB::getDriverName();

        if ($driver === 'sqlite') {
            // Gunakan strftime untuk SQLite
            $data = \App\Models\Expense::selectRaw('
                    strftime("%Y-%m", created_at) as month,
                    SUM(amount) as total
                ')
                ->where('created_at', '>=', now()->subYear())
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();
        } else {
            // Gunakan DATE_FORMAT untuk MySQL / PostgreSQL
            $data = \App\Models\Expense::selectRaw('
                    DATE_FORMAT(created_at, "%Y-%m") as month,
                    SUM(amount) as total
                ')
                ->where('created_at', '>=', now()->subYear())
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();
        }

        // Ubah label bulan jadi nama bulan (contoh: Oktober 2025)
        $data = $data->map(function ($item) {
            $monthName = \Carbon\Carbon::createFromFormat('Y-m', $item->month)->translatedFormat('F Y');
            return [
                'month' => $monthName,
                'total' => $item->total
            ];
        });

        return response()->json($data);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string',
            'amount' => 'required|numeric|min:1',
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update([
            'note' => $request->note,
            'amount' => $request->amount,
        ]);

        return response()->json($expense);
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json(['success' => true]);
    }

}
