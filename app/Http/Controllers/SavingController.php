<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Saving;
use Illuminate\Support\Facades\Auth;

class SavingController extends Controller
{
    public function index()
    {
        $savings = Saving::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSavings = $savings->sum('current_amount');

        return view('savings.index', compact('savings', 'totalSavings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        Saving::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            // 'current_amount' => $request->current_amount ?? 0, // removed to avoid confusion, default is always 0 or set via deposit
            'icon' => $request->icon ?? '💰',
            'color' => $request->color ?? '#0d6efd',
        ]);

        return redirect()->route('savings.index')->with('success', 'Target tabungan berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $saving = Saving::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
        ]);

        $saving->update($request->only(['name', 'target_amount', 'icon', 'color']));

        return redirect()->route('savings.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $saving = Saving::where('user_id', Auth::id())->findOrFail($id);
        $saving->delete();

        return redirect()->route('savings.index')->with('success', 'Target tabungan dihapus!');
    }

    public function deposit(Request $request, $id)
    {
        $saving = Saving::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $saving->current_amount += $request->amount;
        $saving->save();

        return redirect()->route('savings.index')->with('success', 'Berhasil menabung Rp ' . number_format($request->amount, 0, ',', '.') . '!');
    }
}
