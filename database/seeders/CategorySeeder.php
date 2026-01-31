<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'icon' => '🍔', 'color' => '#ef4444'],
            ['name' => 'Transportasi', 'icon' => '🚗', 'color' => '#3b82f6'],
            ['name' => 'Tagihan', 'icon' => '💡', 'color' => '#eab308'],
            ['name' => 'Belanja', 'icon' => '🛒', 'color' => '#10b981'],
            ['name' => 'Hiburan', 'icon' => '🎬', 'color' => '#8b5cf6'],
            ['name' => 'Kesehatan', 'icon' => '💊', 'color' => '#f97316'],
            ['name' => 'Pendidikan', 'icon' => '📚', 'color' => '#06b6d4'],
            ['name' => 'Tabungan & Investasi', 'icon' => '📈', 'color' => '#10b981'],
            ['name' => 'Lainnya', 'icon' => '🔹', 'color' => '#64748b'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
