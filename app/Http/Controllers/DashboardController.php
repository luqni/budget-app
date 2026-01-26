<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\MonthlyIncome;
use App\Models\Category;
use App\Models\Income;
use App\Models\Quote;

use DB;
use Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterMonth = $request->query('month') ?? now()->format('Y-m');
        $userId = Auth::id();
        
        // Daily Quote
        $todaysQuote = Quote::where('is_active_for_date', now()->format('Y-m-d'))->first();
        if (!$todaysQuote) {
            $todaysQuote = Quote::inRandomOrder()->first();
        }

        // Query expense
        $query = Expense::where('user_id', $userId)
            ->with(['category'])
            ->withSum([
                'details as total_realisasi' => function ($q) {
                    $q->where('is_checked', true);
                }
            ], DB::raw('qty * price'))
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($filterMonth) {
            $query->where('month', $filterMonth);
        }

        $expensesRaw = $query->get();
        $groupedExpenses = $expensesRaw->groupBy('month');

        // === NEW: Income dibaca per bulan ===
        $monthlyIncome = MonthlyIncome::where('user_id', $userId)
            ->where('month', $filterMonth)
            ->value('income') ?? 0;

        // Additional Income (Side Job, dll)
        $additionalIncomes = Income::where('user_id', $userId)
            ->where('month', $filterMonth)
            ->orderBy('date', 'desc')
            ->get();
            
        $additionalIncomeTotal = $additionalIncomes->sum('amount');

        // Total Income Real
        $totalIncome = $monthlyIncome + $additionalIncomeTotal;

        // Total alokasi
        $expenseTotal = Expense::where('user_id', $userId)
            ->when($filterMonth, fn($q) => $q->where('month', $filterMonth))
            ->sum('amount');

        // Total realisasi
        $realizationTotal = ExpenseDetail::when($filterMonth, function ($q) use ($filterMonth) {
                $q->whereHas('expense', fn($e) => $e->where('month', $filterMonth));
            })
            ->whereHas('expense', fn($e) => $e->where('user_id', Auth::id()))
            ->where('is_checked', true)
            ->selectRaw('SUM(qty * price) as total')
            ->value('total') ?? 0;

        // Saldo
        $balance = $totalIncome - $realizationTotal;

        // Dropdown bulan (khusus data user ini)
        $months = Expense::where('user_id', $userId)
            ->select('month')
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        // Ensure current month is always available
        $currentMonth = now()->format('Y-m');
        if (!$months->contains($currentMonth)) {
            $months->push($currentMonth);
            $months = $months->sortDesc();
        }

        $categories = Category::all();

        return view('dashboard', [
            'groupedExpenses' => $groupedExpenses, // Keep for backward compatibility if needed
            'expenses' => $expensesRaw, // Pass flat list for new UI
            'income' => $totalIncome, // Total Income (Main + Additional)
            'mainIncome' => $monthlyIncome,
            'additionalIncomes' => $additionalIncomes,
            'expense' => $expenseTotal,
        'realization' => $realizationTotal,
        'totalRealization' => $realizationTotal, // Correction for View
        'balance' => $balance,
            'totalExpense' => $expenseTotal,
            'months' => $months,
            'selectedMonth' => $filterMonth,
            'categories' => $categories,
            'todaysQuote' => $todaysQuote,
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'note' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|date_format:Y-m',
            'category_id' => 'nullable|exists:categories,id',
            'details' => 'nullable|array',
            'details.*.name' => 'required|string',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.price' => 'required|numeric|min:0',
        ]);

        $expense = Expense::create([
            'user_id' => Auth::id(), // ✅ Simpan user ID
            'note' => $data['note'],
            'amount' => $data['amount'],
            'month' => $data['month'],
            'category_id' => $data['category_id'] ?? null,
        ]);

        // Handle Details
        if (!empty($data['details'])) {
            foreach ($data['details'] as $detail) {
                \App\Models\ExpenseDetail::create([
                    'expense_id' => $expense->id,
                    'name' => $detail['name'],
                    'qty' => $detail['qty'],
                    'price' => $detail['price'],
                    'is_checked' => true // Default checked
                ]);
            }
        }

        return response()->json($expense);
    }


    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'note' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'details' => 'nullable|array',
            'details.*.name' => 'required|string',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.price' => 'required|numeric|min:0',
        ]);

        $expense = Expense::where('user_id', Auth::id())->findOrFail($id); // ✅ Cegah edit data user lain

        $expense->update([
            'note' => $request->note,
            'amount' => $request->amount,
            'category_id' => $request->category_id,
        ]);

        // Sync Details if provided (Replace Strategy)
        if (isset($data['details'])) {
            // Delete existing
            $expense->details()->delete();
            
            // Re-create
            foreach ($data['details'] as $detail) {
                \App\Models\ExpenseDetail::create([
                    'expense_id' => $expense->id,
                    'name' => $detail['name'],
                    'qty' => $detail['qty'],
                    'price' => $detail['price'],
                    'is_checked' => true // Default checked on edit sync? Yes.
                ]);
            }
        }

        return response()->json($expense);
    }


    public function destroy($id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        $expense->delete();

        return response()->json(['success' => true]);
    }

    public function chartData(Request $request)
    {
        $userId = Auth::id();
        $targetMonth = $request->query('month'); // Format 'Y-m', optional

        // Logic: 
        // If specific month is selected -> Show Daily breakdown for that month?
        // Or keep Bar Chart showing Monthly trend for the YEAR of selected month?
        // Let's stick to: Bar Chart shows Monthly Trend for 12 months ending at selected month (or current)
        
        $endDate = $targetMonth ? \Carbon\Carbon::createFromFormat('Y-m', $targetMonth)->endOfMonth() : now()->endOfMonth();
        $startDate = $endDate->copy()->subMonths(11)->startOfMonth();

        $driver = \DB::getDriverName();

        if ($driver === 'sqlite') {
            $data = \App\Models\Expense::selectRaw('month, SUM(amount) as total')
                ->whereBetween('month', [$startDate->format('Y-m'), $endDate->format('Y-m')])
                ->where('user_id', $userId)
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();
        } else {
            $data = \App\Models\Expense::selectRaw('month, SUM(amount) as total')
                ->whereBetween('month', [$startDate->format('Y-m'), $endDate->format('Y-m')])
                ->where('user_id', $userId)
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();
        }

        $formatted = $data->map(function ($item) {
            try {
                $monthName = \Carbon\Carbon::createFromFormat('Y-m', $item->month)->translatedFormat('M y');
            } catch (\Exception $e) {
                $monthName = $item->month;
            }
            return [
                'month' => $monthName,
                'total' => (int) $item->total,
            ];
        });

        return response()->json($formatted);
    }

    public function chartDataCategory(Request $request)
    {
        $userId = Auth::id();
        $targetMonth = $request->query('month'); // 'Y-m'

        $query = Expense::where('user_id', $userId);

        if ($targetMonth) {
            // Filter specific month
            $query->where('month', $targetMonth);
        } else {
            // Default: Current month
            $query->where('month', now()->format('Y-m'));
        }
        
        $data = $query->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total') // Sort largest first
            ->with('category')
            ->get();

        $totalExpense = $data->sum('total');
        
        $formatted = $data->map(function ($item) use ($totalExpense) {
            $percentage = $totalExpense > 0 ? round(($item->total / $totalExpense) * 100, 1) : 0;
            return [
                'name' => $item->category ? $item->category->name : 'Lainnya',
                'icon' => $item->category ? $item->category->icon : '❓',
                'color' => '#0d6efd', // We'll handle colors in frontend
                'total' => (int) $item->total,
                'percentage' => $percentage
            ];
        });
        
        return response()->json($formatted);
    }


    public function storeIncome(Request $request)
    {
        $request->validate([
            'income' => 'required|integer',
            'monthIncome' => 'required|date_format:Y-m',
        ]);
        MonthlyIncome::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'month' => $request->monthIncome
            ],
            [
                'income' => $request->income
            ]
        );
        return redirect()->route('dashboard')->with('success', 'Pemasukan awal berhasil disimpan.');
    }

    public function updateIncome(Request $request)
    {
        $request->validate([
            'income' => 'required|integer',
            'monthIncome' => 'required|date_format:Y-m',
        ]);
        MonthlyIncome::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'month' => $request->monthIncome
            ],
            [
                'income' => $request->income
            ]
        );
        return redirect()->route('dashboard')->with('success', 'Pemasukan berhasil diperbarui.');
    }

    public function storeAdditionalIncome(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        Income::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'month' => date('Y-m', strtotime($request->date)),
        ]);

        return redirect()->back()->with('success', 'Pemasukan tambahan berhasil disimpan!');
    }

    public function destroyAdditionalIncome($id)
    {
        $income = Income::where('user_id', Auth::id())->findOrFail($id);
        $income->delete();

        return redirect()->back()->with('success', 'Pemasukan tambahan dihapus.');
    }

    public function countUsers(){
        $totalUsers = \App\Models\User::count();
        
        return view('user', compact('totalUsers'));
    }

    public function getDataAlokasi(Request $request){
        $userId = Auth::id();
        $filterMonth = $request->query('month');
        if (!$filterMonth) $filterMonth = now()->format('Y-m');

        $expenseTotal = Expense::where('user_id', $userId)
            ->when($filterMonth, fn($q)=>$q->where('month',$filterMonth))
            ->sum('amount');


        return response()->json($expenseTotal);

    }

    public function getDataRealisasi(Request $request){
        $userId = Auth::id();
        $filterMonth = $request->query('month');
        if (!$filterMonth) $filterMonth = now()->format('Y-m');

        // Total Realisasi (Synced to Expense Table)
        $realizationTotal = Expense::where('user_id', $userId)
            ->when($filterMonth, fn($q)=>$q->where('month', $filterMonth))
            ->sum('amount');


        return response()->json($realizationTotal);

    }

    public function getIncome(Request $request){
        $userId = Auth::id();
        $filterMonth = $request->query('month');
        if (!$filterMonth) $filterMonth = now()->format('Y-m');
        
        \Log::info("getIncome: User $userId, Month $filterMonth");

        // === Logic matches index() ===
        $monthlyIncome = MonthlyIncome::where('user_id', $userId)
            ->where('month', $filterMonth)
            ->value('income') ?? 0;
            
        \Log::info("getIncome: Monthly $monthlyIncome");

        $additionalIncomeTotal = Income::where('user_id', $userId)
            ->where('month', $filterMonth)
            ->sum('amount');
            
        \Log::info("getIncome: Additional $additionalIncomeTotal");

        $totalIncome = $monthlyIncome + $additionalIncomeTotal;
        \Log::info("getIncome: Total $totalIncome");

        return response()->json($totalIncome);
    }
}
