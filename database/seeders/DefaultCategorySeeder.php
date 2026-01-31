<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class DefaultCategorySeeder extends Seeder
{
    /**
     * Default categories that will be created for each new user
     */
    public static function getDefaultCategories()
    {
        return [
            [
                'name' => 'Makanan & Minuman',
                'icon' => '🍔',
                'color' => '#ef4444',
                'budget_limit' => null
            ],
            [
                'name' => 'Transportasi',
                'icon' => '🚗',
                'color' => '#3b82f6',
                'budget_limit' => null
            ],
            [
                'name' => 'Belanja',
                'icon' => '🛒',
                'color' => '#8b5cf6',
                'budget_limit' => null
            ],
            [
                'name' => 'Tagihan',
                'icon' => '💳',
                'color' => '#f59e0b',
                'budget_limit' => null
            ],
            [
                'name' => 'Kesehatan',
                'icon' => '🏥',
                'color' => '#10b981',
                'budget_limit' => null
            ],
            [
                'name' => 'Pendidikan',
                'icon' => '📚',
                'color' => '#06b6d4',
                'budget_limit' => null
            ],
            [
                'name' => 'Hiburan',
                'icon' => '🎮',
                'color' => '#ec4899',
                'budget_limit' => null
            ],
            [
                'name' => 'Sedekah',
                'icon' => '🤲',
                'color' => '#14b8a6',
                'budget_limit' => null
            ],
            [
                'name' => 'Lainnya',
                'icon' => '📝',
                'color' => '#6b7280',
                'budget_limit' => null
            ],
        ];
    }

    /**
     * Create default categories for a specific user
     */
    public static function createForUser($userId)
    {
        $categories = self::getDefaultCategories();
        
        foreach ($categories as $category) {
            Category::create(array_merge($category, ['user_id' => $userId]));
        }
    }

    /**
     * Run the database seeds (for existing users without categories)
     */
    public function run(): void
    {
        // This can be used to backfill categories for existing users
        $users = \App\Models\User::all();
        
        foreach ($users as $user) {
            // Check if user already has categories
            if ($user->categories()->count() === 0) {
                self::createForUser($user->id);
            }
        }
    }
}
