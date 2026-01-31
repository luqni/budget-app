<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Auth;

class CategoryController extends Controller
{
    public function index()
    {
        // Only show categories for current user
        $categories = Category::where('user_id', Auth::id())->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:10',
            'color' => 'required|string|max:7',
            'budget_limit' => 'nullable|numeric|min:0|max:9999999999999',
        ]);

        // Create category for current user
        Category::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'icon' => $request->icon,
            'color' => $request->color,
            'budget_limit' => $request->budget_limit,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:10',
            'color' => 'required|string|max:7',
            'budget_limit' => 'nullable|numeric|min:0|max:9999999999999',
        ]);

        // Only update if category belongs to current user
        $category = Category::where('user_id', Auth::id())->findOrFail($id);
        $category->update($request->only(['name', 'icon', 'color', 'budget_limit']));

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Only delete if category belongs to current user
        $category = Category::where('user_id', Auth::id())->findOrFail($id);
        
        // Check if category is used
        if ($category->expenses()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Kategori tidak bisa dihapus karena masih digunakan!');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
