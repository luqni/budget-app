<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\User;
use App\Models\MonthlyIncome;
use App\Models\Income;
use App\Models\Debt;
use App\Models\Saving;
use Carbon\Carbon;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AiFinanceController extends Controller
{
    public function ask(Request $request)
    {
        $user = $request->user();
        $today = Carbon::now()->format('Y-m-d');
        $key = 'ai_finance:' . $user->id;

        // 1. Rate Limiting (3 requests per day)
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'answer' => "Maaf, kamu sudah mencapai batas tanya Asisten Keuangan hari ini (3x). Coba lagi besok ya! \n\n" . $this->getFallbackSummary($user),
                'limit_reached' => true,
                'retry_after' => $seconds
            ]);
        }

        // 2. Increment immediately (Prevent Race Condition)
        RateLimiter::hit($key, 86400);

        // 3. Prepare Context Data
        $contextData = $this->getFinancialContext($user);
        $question = $request->input('question');

        // 4. Call Gemini API
        $apiKey = config('services.gemini.api_key');
        $baseUrl = config('services.gemini.base_url');
        
        Log::info('AI Ask Request', [
            'user_id' => $user->id,
            'api_key_exists' => !empty($apiKey),
            'base_url' => $baseUrl,
            'question_length' => strlen($question)
        ]);

        if (!$apiKey) {
            Log::warning('AI Finance: API Key missing');
            return response()->json(['answer' => "Fitur AI belum dikonfigurasi sepenuhnya. Hubungi admin."]);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$baseUrl}/v1beta/models/gemini-2.5-flash-lite:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $this->buildPrompt($contextData, $question)]
                        ]
                    ]
                ]
            ]);

            Log::info('Gemini API Response Status: ' . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                $answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak bisa menjawab saat ini.';
                
                return response()->json([
                    'answer' => $answer,
                    'usage' => RateLimiter::attempts($key),
                    'remaining' => RateLimiter::remaining($key, 3)
                ]);
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json([
                    'answer' => "Maaf, sedang ada gangguan pada layanan AI. \n\n" . $this->getFallbackSummary($user)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('AI Finance Error: ' . $e->getMessage());
            return response()->json([
                'answer' => "Terjadi kesalahan sistem. \n\n" . $this->getFallbackSummary($user)
            ]);
        }
    }

    private function getFinancialContext($user)
    {
        $month = Carbon::now()->format('Y-m');
        
        $totalExpense = Expense::where('user_id', $user->id)->where('month', $month)->sum('amount');
        $mainIncome = MonthlyIncome::where('user_id', $user->id)->where('month', $month)->value('income') ?? 0;
        $addIncome = Income::where('user_id', $user->id)->where('month', $month)->sum('amount');
        $totalIncome = $mainIncome + $addIncome;
        $balance = $totalIncome - $totalExpense;
        
        $topCategories = Expense::where('user_id', $user->id)
            ->where('month', $month)
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(function($item) {
                return ($item->category->name ?? 'Lainnya') . ': ' . number_format($item->total, 0, ',', '.');
            })->implode(', ');

        // Debt & Receivables
        $totalPayable = Debt::where('user_id', $user->id)->where('type', 'payable')->where('status', 'unpaid')->sum('amount');
        $totalReceivable = Debt::where('user_id', $user->id)->where('type', 'receivable')->where('status', 'unpaid')->sum('amount');

        // Savings
        $totalSavings = Saving::where('user_id', $user->id)->sum('current_amount');
        $savingGoals = Saving::where('user_id', $user->id)->get()->map(function($s) {
            return "{$s->name} (" . number_format($s->current_amount, 0, ',', '.') . " / " . number_format($s->target_amount, 0, ',', '.') . ")";
        })->implode(', ');

        return "Bulan: " . Carbon::now()->translatedFormat('F Y') . "\n" .
               "Pemasukan: Rp " . number_format($totalIncome, 0, ',', '.') . "\n" .
               "Pengeluaran: Rp " . number_format($totalExpense, 0, ',', '.') . "\n" .
               "Sisa Saldo: Rp " . number_format($balance, 0, ',', '.') . "\n" .
               "Top Pengeluaran: " . $topCategories . "\n\n" .
               "Hutang (Harus dibayar): Rp " . number_format($totalPayable, 0, ',', '.') . "\n" .
               "Piutang (Uang di orang): Rp " . number_format($totalReceivable, 0, ',', '.') . "\n" .
               "Total Tabungan Terkumpul: Rp " . number_format($totalSavings, 0, ',', '.') . "\n" .
               "Target Tabungan: " . ($savingGoals ?: '-');
    }

    private function buildPrompt($context, $question)
    {
        return "Kamu adalah asisten keuangan pribadi yang bijak dan ramah bernama 'Qanaah AI'. \n" .
               "Gunakan data keuangan berikut untuk menjawab pertanyaan user:\n\n" .
               $context . "\n\n" .
               "Pertanyaan User: \"{$question}\"\n\n" .
               "Jawablah dengan ringkas, jelas, dan memotivasi untuk berhemat atau bijak mengatur uang (gunakan prinsip Qanaah/rasa cukup). " .
               "Jika pertanyaan tidak relevan dengan keuangan, jawab dengan sopan bahwa kamu hanya fokus pada keuangan.";
    }

    private function getFallbackSummary($user)
    {
        // Simple summary without AI
        $context = $this->getFinancialContext($user);
        return "Ringkasan Data Kamu:\n" . $context;
    }

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
