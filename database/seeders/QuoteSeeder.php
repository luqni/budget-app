<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quote;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            [
                'content' => 'Tidak akan bergeser kaki seorang hamba pada hari kiamat sampai ia ditanya tentang umurnya untuk apa ia habiskan, ilmunya untuk apa ia amalkan, hartanya dari mana ia peroleh dan kemana ia belanjakan, serta tubuhnya untuk apa ia gunakan.',
                'source' => 'HR. Tirmidzi',
                'type' => 'hadith'
            ],
            [
                'content' => 'Allah akan memberikan rahmat kepada seseorang yang berusaha mencari harta dengan jalan yang halal, dan menyedekahkan harta tersebut dengan jalan yang benar.',
                'source' => 'HR. Bukhari',
                'type' => 'hadith'
            ],
            [
                'content' => 'Sesungguhnya pemboros-pemboros itu adalah saudara-saudara syaitan.',
                'source' => 'QS. Al-Isra: 27',
                'type' => 'quran'
            ],
            [
                'content' => 'Jangan menyimpan apa yang tersisa setelah belanja, tapi belanjakan apa yang tersisa setelah menabung.',
                'source' => 'Warren Buffett',
                'type' => 'quote'
            ],
            [
                'content' => 'Bukan seberapa banyak uang yang kamu hasilkan, tapi seberapa banyak uang yang bisa kamu simpan.',
                'source' => 'Robert Kiyosaki',
                'type' => 'quote'
            ],
            [
                'content' => 'Harta tidak akan berkurang karena sedekah.',
                'source' => 'HR. Muslim',
                'type' => 'hadith'
            ],
            [
                'content' => 'Kekayaan bukanlah dengan banyaknya harta, namun kekayaan adalah kekayaan hati (qanaah).',
                'source' => 'HR. Bukhari & Muslim',
                'type' => 'hadith'
            ],
             [
                'content' => 'Sebaik-baik harta adalah harta yang berada dalam penguasaan orang saleh.',
                'source' => 'HR. Ahmad',
                'type' => 'hadith'
            ],
            [
                'content' => 'Hutang adalah beban di malam hari dan kehinaan di siang hari.',
                'source' => 'Umar bin Khattab',
                'type' => 'quote'
            ],
             [
                'content' => 'Menunda-nunda pembayaran hutang bagi orang yang mampu adalah kezaliman.',
                'source' => 'HR. Bukhari',
                'type' => 'hadith'
            ],
        ];

        foreach ($quotes as $q) {
            Quote::firstOrCreate(['content' => $q['content']], $q);
        }
    }
}
