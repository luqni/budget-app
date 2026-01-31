<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\User;
use App\Models\MonthlyIncome;
use App\Models\Income;
use Carbon\Carbon;

class AiFinanceController extends Controller
{
    public function summary(Request $request)
    {
        $user = $request->user();

        // Ambil bulan sekarang
        $month = Carbon::now()->format('Y-m');

        // Total pengeluaran bulan ini
        $totalExpense = Expense::where('user_id', $user->id)
            ->where('month', $month)
            ->sum('amount');

        // Hitung detail berdasarkan kategori/item
        $details = ExpenseDetail::whereHas('expense', function ($q) use ($user, $month) {
            $q->where('user_id', $user->id)
              ->where('month', $month);
        })->get();

        // Hitung total per item
        $grouped = $details->groupBy('name')->map(function ($items) {
            return [
                'total_qty' => $items->sum('qty'),
                'total_spent' => $items->sum(fn($d) => $d->qty * $d->price),
            ];
        });

        // Total pemasukan correct
        $mainIncome = MonthlyIncome::where('user_id', $user->id)
            ->where('month', $month)
            ->value('income') ?? 0;
            
        $addIncome = Income::where('user_id', $user->id)
            ->where('month', $month)
            ->sum('amount');
            
        $income = $mainIncome + $addIncome;

        // Sisa saldo
        $balance = $income - $totalExpense;

        return response()->json([
            'month' => Carbon::now()->translatedFormat('F Y'),
            'income' => (int) $income,
            'total_expense' => $totalExpense,
            'balance' => $balance,
            'categories' => $grouped,
        ]);
    }

    public function details(Request $request)
    {
        $user = $request->user();

        $expenses = Expense::with(['details' => function ($q) {
            $q->select('expense_id', 'name', 'qty', 'price', 'is_checked');
        }])
        ->where('user_id', $user->id)
        ->latest()
        ->take(5)
        ->get(['id', 'note', 'amount', 'month', 'created_at']);

        return response()->json([
            'notes' => $expenses,
        ]);
    }

    public function context($userId)
    {

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $month = Carbon::now()->format('Y-m');

        $totalExpense = Expense::where('user_id', $user->id)
            ->where('month', $month)
            ->sum('amount');

        $mainIncome = MonthlyIncome::where('user_id', $user->id)
            ->where('month', $month)
            ->value('income') ?? 0;

        $addIncome = Income::where('user_id', $user->id)
            ->where('month', $month)
            ->sum('amount');

        $income = $mainIncome + $addIncome;

        $balance = $income - $totalExpense;

        $topCategories = Expense::where('user_id', $user->id)
            ->where('month', $month)
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $text = "Ringkasan keuangan {$user->name} bulan " . Carbon::now()->translatedFormat('F Y') . ":\n";
        $text .= "- Total pemasukan: Rp " . number_format($income, 0, ',', '.') . "\n";
        $text .= "- Total pengeluaran: Rp " . number_format($totalExpense, 0, ',', '.') . "\n";
        $text .= "- Sisa saldo: Rp " . number_format($balance, 0, ',', '.') . "\n\n";
        $text .= "5 kategori pengeluaran terbesar:\n";

        if ($topCategories->isEmpty()) {
            $text .= "- Belum ada pengeluaran.\n";
        } else {
            foreach ($topCategories as $item) {
                $catName = $item->category ? $item->category->name : 'Lainnya';
                $text .= "• {$catName}: Rp " . number_format($item->total, 0, ',', '.') . "\n";
            }
        }

        return response()->json([
            'context' => $text,
        ]);
    }
}
