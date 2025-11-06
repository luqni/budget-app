<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use DB;
use Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterMonth = $request->query('month');
        $userId = Auth::id(); // ✅ Ambil ID user login

        $query = Expense::where('user_id', $userId) // ✅ Filter data berdasarkan user
            ->withSum([
                'details as total_realisasi' => function ($q) {
                    $q->where('is_checked', true);
                }
            ], DB::raw('qty * price'))
            ->orderBy('month', 'desc');

        if ($filterMonth) {
            $query->where('month', $filterMonth);
        }

        $expenses = $query->get()->groupBy('month');

        // Total Income
        $income = Auth::user()->income ?? 0;

        // Total Alokasi
        $expenseTotal = Expense::where('user_id', $userId)
            ->when($filterMonth, fn($q)=>$q->where('month',$filterMonth))
            ->sum('amount');

        // Total Realisasi
        $realizationTotal = ExpenseDetail::when($filterMonth, function($q) use ($filterMonth) {
                $q->whereHas('expense', fn($e)=>$e->where('month', $filterMonth));
            })
            ->whereHas('expense', fn($e)=>$e->where('user_id', Auth::id())) // ✅ Filter by user
            ->where('is_checked', true)
            ->selectRaw('SUM(qty * price) as total')
            ->value('total') ?? 0;

        // Saldo
        $balance = $income - $realizationTotal;

        // Dropdown bulan (khusus data user ini)
        $months = Expense::where('user_id', $userId)
            ->select('month')
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        return view('dashboard', [
            'groupedExpenses' => $expenses,
            'income' => $income,
            'expense' => $expenseTotal,
            'realization' => $realizationTotal,
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
            'user_id' => Auth::id(), // ✅ Simpan user ID
            'note' => $data['note'],
            'month' => $data['month'],
            'amount' => 0,
        ]);

        return response()->json($expense);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string',
            'amount' => 'required|numeric|min:1',
        ]);

        $expense = Expense::where('user_id', Auth::id())->findOrFail($id); // ✅ Cegah edit data user lain

        $expense->update([
            'note' => $request->note,
            'amount' => $request->amount,
        ]);

        return response()->json($expense);
    }


    public function destroy($id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        $expense->delete();

        return response()->json(['success' => true]);
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


    public function storeIncome(Request $request)
    {
        $request->validate(['income' => 'required|integer']);
        auth()->user()->update(['income' => $request->income]);
        return redirect()->route('dashboard')->with('success', 'Pemasukan awal berhasil disimpan.');
    }

    public function updateIncome(Request $request)
    {
        $request->validate(['income' => 'required|integer']);
        auth()->user()->update(['income' => $request->income]);
        return redirect()->route('dashboard')->with('success', 'Pemasukan berhasil diperbarui.');
    }

    public function countUsers(){
        $totalUsers = \App\Models\User::count();
        
        return view('user', compact('totalUsers'));
    }
}
