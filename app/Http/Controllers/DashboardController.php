<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterMonth = $request->query('month');

        $query = Expense::withSum([
            'details as total_realisasi' => function ($q) {
                $q->where('is_checked', true);
            }
        ], DB::raw('qty * price'))
        ->orderBy('month', 'desc');

        if ($filterMonth) {
            $query->where('month', $filterMonth);
        }

        $expenses = $query->get()->groupBy('month');

        // Total Income (sementara masih statis)
        $income = 5000000;

        // Total Alokasi (jumlah amount dari tabel expenses)
        $expenseTotal = Expense::when($filterMonth, fn($q)=>$q->where('month',$filterMonth))->sum('amount');

        // Total Realisasi (jumlah semua total_realisasi dari setiap expense)
        $realizationTotal = ExpenseDetail::when($filterMonth, function($q) use ($filterMonth) {
            $q->whereHas('expense', fn($e)=>$e->where('month', $filterMonth));
        })
        ->where('is_checked', true)
        ->selectRaw('SUM(qty * price) as total')
        ->value('total') ?? 0;

        // Saldo
        $balance = $income - $realizationTotal;

        // Dropdown bulan
        $months = Expense::select('month')->distinct()->orderBy('month', 'desc')->pluck('month');

        return view('dashboard', [
            'groupedExpenses' => $expenses,
            'income' => $income,
            'expense' => $expenseTotal, // Total Alokasi
            'realization' => $realizationTotal, // Total Realisasi
            'balance' => $balance,
            'totalExpense' => $expenseTotal,
            'months' => $months,
            'selectedMonth' => $filterMonth,
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'note' => 'required|string|max:255',
            'month' => 'required|date_format:Y-m',
        ]);

        $expense = Expense::create([
            'note' => $data['note'],
            'month' => $data['month'],
            'amount' => 0 // default
        ]);

        return response()->json($expense);
    }

    public function chartData()
    {
        $driver = \DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite tidak perlu DATE_FORMAT, cukup ambil langsung kolom month
            $data = \App\Models\Expense::selectRaw('
                    month,
                    SUM(amount) as total
                ')
                ->where('month', '>=', now()->subYear()->format('Y-m'))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();
        } else {
            // MySQL / PostgreSQL
            $data = \App\Models\Expense::selectRaw('
                    month,
                    SUM(amount) as total
                ')
                ->where('month', '>=', now()->subYear()->format('Y-m'))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();
        }

        // Ubah format bulan jadi nama bulan lengkap (misalnya: Oktober 2025)
        $data = $data->map(function ($item) {
            try {
                $monthName = \Carbon\Carbon::createFromFormat('Y-m', $item->month)->translatedFormat('F Y');
            } catch (\Exception $e) {
                $monthName = $item->month; // fallback kalau format tidak valid
            }

            return [
                'month' => $monthName,
                'total' => (int) $item->total,
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
