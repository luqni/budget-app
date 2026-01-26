<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quote;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/quotes.json');
        
        if (file_exists($path)) {
            $json = file_get_contents($path);
            $quotes = json_decode($json, true);
        } else {
            // Fallback content if file not found
            $quotes = [
                // ... stored fallback ...
                 [
                    'content' => 'Tidak akan bergeser kaki seorang hamba pada hari kiamat sampai ia ditanya tentang umurnya untuk apa ia habiskan, ilmunya untuk apa ia amalkan, hartanya dari mana ia peroleh dan kemana ia belanjakan, serta tubuhnya untuk apa ia gunakan.',
                    'source' => 'HR. Tirmidzi',
                    'type' => 'hadith'
                ],
            ];
        }

        foreach ($quotes as $q) {
            Quote::firstOrCreate(['content' => $q['content']], $q);
        }
        
        // Ensure one quote is active for today if none exists
        $today = now()->format('Y-m-d');
        if (!Quote::where('is_active_for_date', $today)->exists()) {
             $quote = Quote::inRandomOrder()->first();
             if($quote) {
                 $quote->update(['is_active_for_date' => $today]);
             }
        }
    }
}
