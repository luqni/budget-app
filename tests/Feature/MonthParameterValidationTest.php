<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MonthParameterValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
    }

    /**
     * Test valid month format is accepted
     *
     * @return void
     */
    public function test_valid_month_format_is_accepted()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/chart-category-data?month=2026-01');

        $response->assertStatus(200);
    }

    /**
     * Test XSS attack via script tag is blocked
     *
     * @return void
     */
    public function test_xss_script_injection_is_blocked()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/chart-category-data?month=2026-03<script>alert(1)</script>');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['month']);
    }

    /**
     * Test XSS attack via img tag is blocked
     *
     * @return void
     */
    public function test_xss_img_injection_is_blocked()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/chart-category-data?month=2026-03<img src=x>');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['month']);
    }

    /**
     * Test invalid month number is blocked
     *
     * @return void
     */
    public function test_invalid_month_number_is_blocked()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/chart-category-data?month=2026-13');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['month']);
    }

    /**
     * Test malformed month format is blocked
     *
     * @return void
     */
    public function test_malformed_month_format_is_blocked()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/chart-category-data?month=26-01');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['month']);
    }

    /**
     * Test random string injection is blocked
     *
     * @return void
     */
    public function test_random_string_injection_is_blocked()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/chart-category-data?month=2026-03abdabdnm');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['month']);
    }

    /**
     * Test SQL injection attempt is blocked
     *
     * @return void
     */
    public function test_sql_injection_attempt_is_blocked()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/chart-category-data?month=2026-01\' OR 1=1--');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['month']);
    }

    /**
     * Test all endpoints with month parameter
     *
     * @return void
     */
    public function test_all_endpoints_validate_month_parameter()
    {
        $endpoints = [
            '/dashboard',
            '/chart-data',
            '/chart-category-data',
            '/summary/alokasi',
            '/summary/realisasi',
            '/summary/income',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->actingAs($this->user)
                ->getJson($endpoint . '?month=2026-03<script>xss</script>');

            $this->assertTrue(
                $response->status() === 422 || $response->status() === 302,
                "Endpoint {$endpoint} should reject invalid month format"
            );
        }
    }

    /**
     * Test validation error message is in Indonesian
     *
     * @return void
     */
    public function test_validation_error_message_is_in_indonesian()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/chart-category-data?month=invalid');

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'month' => [
                        'Format bulan tidak valid. Gunakan format YYYY-MM (contoh: 2026-01).'
                    ]
                ]
            ]);
    }
}
