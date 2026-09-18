<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Expense;
use App\Models\Income;
use App\Models\MonthlyIncome;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_page_can_be_rendered_when_empty(): void
    {
        $response = $this->get('/user');

        $response->assertStatus(200);
        $response->assertSee('Data Pengguna');
        $response->assertSee('Pengguna Aktif (30 Hari)');
        $response->assertSee('Semua Pengguna Terdaftar');
        $response->assertSee('Belum Ada Pengguna Aktif');
        $response->assertSee('Tidak ada pengguna ditemukan');
    }

    public function test_user_page_displays_all_users_and_active_users_without_emails(): void
    {
        $userActive = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com'
        ]);

        $userInactive = User::factory()->create([
            'name' => 'Ani Wijaya',
            'email' => 'ani@example.com'
        ]);

        // User active adds an expense in the last 30 days
        Expense::create([
            'user_id' => $userActive->id,
            'note' => 'Beli kopi',
            'amount' => 25000,
            'month' => now()->format('Y-m'),
            'date' => now()->format('Y-m-d'),
        ]);

        $response = $this->get('/user');

        $response->assertStatus(200);

        // Check both users names appear
        $response->assertSee('Budi Santoso');
        $response->assertSee('Ani Wijaya');

        // Check count badges
        $response->assertSee('1 User');
        $response->assertSee('2 Pengguna');

        // Check active badge and inactive badge
        $response->assertSee('Aktif');
        $response->assertSee('Tidak Aktif');

        // Privacy check: Email addresses must NOT be visible
        $response->assertDontSee('budi@example.com');
        $response->assertDontSee('ani@example.com');
        $response->assertDontSee('EMAIL');
    }

    public function test_user_active_via_income_or_monthly_income(): void
    {
        $userIncome = User::factory()->create(['name' => 'Citra Lestari', 'email' => 'citra@example.com']);
        Income::create([
            'user_id' => $userIncome->id,
            'title' => 'Bonus Project',
            'amount' => 500000,
            'month' => now()->format('Y-m'),
            'date' => now()->format('Y-m-d'),
        ]);

        $userMonthly = User::factory()->create(['name' => 'Dewi Sartika', 'email' => 'dewi@example.com']);
        MonthlyIncome::create([
            'user_id' => $userMonthly->id,
            'income' => 5000000,
            'month' => now()->format('Y-m'),
        ]);

        $response = $this->get('/user');

        $response->assertStatus(200);
        $response->assertSee('Citra Lestari');
        $response->assertSee('Dewi Sartika');
        $response->assertSee('2 User'); // Active users count

        // Privacy check: Email addresses must NOT be visible
        $response->assertDontSee('citra@example.com');
        $response->assertDontSee('dewi@example.com');
    }
}
