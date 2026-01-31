<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quote;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            // QURAN - Ayat tentang Rezeki, Sedekah, Syukur
            [
                'content' => 'Dan tidak ada suatu binatang melata pun di bumi melainkan Allah-lah yang memberi rezekinya, dan Dia mengetahui tempat berdiam binatang itu dan tempat penyimpanannya. Semua itu tertulis dalam Kitab yang nyata (Lauh Mahfuzh).',
                'source' => 'QS. Hud: 6',
                'type' => 'quran',
                'category' => 'quran'
            ],
            [
                'content' => 'Maka apabila kamu telah selesai (dari sesuatu urusan), kerjakanlah dengan sungguh-sungguh (urusan) yang lain, dan hanya kepada Tuhanmulah hendaknya kamu berharap.',
                'source' => 'QS. Al-Insyirah: 7-8',
                'type' => 'quran',
                'category' => 'quran'
            ],
            [
                'content' => 'Perumpamaan (nafkah yang dikeluarkan oleh) orang-orang yang menafkahkan hartanya di jalan Allah adalah serupa dengan sebutir benih yang menumbuhkan tujuh bulir, pada tiap-tiap bulir seratus biji. Allah melipat gandakan (ganjaran) bagi siapa yang Dia kehendaki.',
                'source' => 'QS. Al-Baqarah: 261',
                'type' => 'quran',
                'category' => 'quran'
            ],
            [
                'content' => 'Hai orang-orang yang beriman, janganlah kamu saling memakan harta sesamamu dengan jalan yang batil, kecuali dengan jalan perniagaan yang berlaku dengan suka sama suka di antara kamu.',
                'source' => 'QS. An-Nisa: 29',
                'type' => 'quran',
                'category' => 'quran'
            ],
            [
                'content' => 'Dan carilah pada apa yang telah dianugerahkan Allah kepadamu (kebahagiaan) negeri akhirat, dan janganlah kamu melupakan bahagianmu dari (kenikmatan) duniawi.',
                'source' => 'QS. Al-Qasas: 77',
                'type' => 'quran',
                'category' => 'quran'
            ],
            [
                'content' => 'Sesungguhnya Allah menyukai orang yang beriman yang bekerja (mencari rezeki).',
                'source' => 'QS. At-Taubah: 105',
                'type' => 'quran',
                'category' => 'quran'
            ],
            [
                'content' => 'Barangsiapa yang mensyukuri (nikmat Allah), maka sesungguhnya dia mensyukuri untuk dirinya sendiri; dan barangsiapa yang tidak bersyukur, maka sesungguhnya Allah Maha Kaya lagi Maha Terpuji.',
                'source' => 'QS. Luqman: 12',
                'type' => 'quran',
                'category' => 'quran'
            ],
            [
                'content' => 'Dan Kami perintahkan kepada manusia (berbuat baik) kepada dua orang ibu-bapaknya; ibunya telah mengandungnya dalam keadaan lemah yang bertambah-tambah, dan menyapihnya dalam dua tahun. Bersyukurlah kepada-Ku dan kepada dua orang ibu bapakmu, hanya kepada-Kulah kembalimu.',
                'source' => 'QS. Luqman: 14',
                'type' => 'quran',
                'category' => 'quran'
            ],
            [
                'content' => 'Maka nikmat Tuhan kamu yang manakah yang kamu dustakan?',
                'source' => 'QS. Ar-Rahman: 13',
                'type' => 'quran',
                'category' => 'quran'
            ],
            [
                'content' => 'Dan jika kamu menghitung nikmat Allah, niscaya kamu tidak akan mampu menghitungnya. Sungguh, Allah benar-benar Maha Pengampun, Maha Penyayang.',
                'source' => 'QS. An-Nahl: 18',
                'type' => 'quran',
                'category' => 'quran'
            ],

            // HADITS - Tentang Keuangan dan Muamalah
            [
                'content' => 'Tidak akan bergeser kaki seorang hamba pada hari kiamat sampai ia ditanya tentang umurnya untuk apa ia habiskan, ilmunya untuk apa ia amalkan, hartanya dari mana ia peroleh dan kemana ia belanjakan, serta tubuhnya untuk apa ia gunakan.',
                'source' => 'HR. Tirmidzi',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Tangan di atas lebih baik daripada tangan di bawah. Mulailah (memberi) kepada orang yang menjadi tanggunganmu.',
                'source' => 'HR. Bukhari & Muslim',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Sebaik-baik manusia adalah yang paling bermanfaat bagi manusia.',
                'source' => 'HR. Ahmad, ath-Thabrani',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Barangsiapa yang menghilangkan kesusahan seorang mukmin dari berbagai kesusahan-kesusahan dunia, niscaya Allah akan menghilangkan kesusahannya pada hari kiamat.',
                'source' => 'HR. Muslim',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Sedekah tidak akan mengurangi harta. Tidaklah seseorang yang memaafkan (orang lain) melainkan Allah akan menambah kemuliaannya.',
                'source' => 'HR. Muslim',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Harta yang paling baik adalah harta yang halal dan paling baik dari harta yang halal adalah yang dihasilkan dari usaha tangan sendiri.',
                'source' => 'HR. Ahmad',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Janganlah kamu menjual sesuatu yang tidak ada padamu.',
                'source' => 'HR. Abu Dawud, Tirmidzi, Nasa\'i, Ibnu Majah',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Pedagang yang jujur dan terpercaya akan bersama para nabi, orang-orang shiddiq, dan para syuhada.',
                'source' => 'HR. Tirmidzi',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Barangsiapa yang berhutang dengan niat akan melunasinya, maka Allah akan melunasi hutangnya. Dan barangsiapa yang berhutang dengan niat tidak akan melunasinya, maka Allah akan menghancurkannya.',
                'source' => 'HR. Bukhari',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Sesungguhnya Allah mencintai seorang hamba yang bekerja dan terampil.',
                'source' => 'HR. Baihaqi',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Mencari rezeki yang halal adalah kewajiban setelah kewajiban (fardhu).',
                'source' => 'HR. Thabrani',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Zakat harta adalah membersihkannya dan obat bagi orang sakit adalah sedekah.',
                'source' => 'HR. Thabrani',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Tidak ada hari dimana pagi harinya seorang hamba berada di dalamnya melainkan ada dua malaikat yang turun. Salah satunya berkata: Ya Allah, berikanlah ganti kepada orang yang berinfak. Dan yang lainnya berkata: Ya Allah, berikanlah kebinasaan kepada orang yang menahan (hartanya).',
                'source' => 'HR. Bukhari & Muslim',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Orang yang kuat bukanlah orang yang pandai bergulat, tetapi orang yang kuat adalah orang yang mampu mengendalikan dirinya ketika marah.',
                'source' => 'HR. Bukhari & Muslim',
                'type' => 'hadith',
                'category' => 'hadits'
            ],
            [
                'content' => 'Kekayaan yang sebenarnya bukanlah banyaknya harta benda, tetapi kekayaan yang sebenarnya adalah kekayaan jiwa (qana\'ah).',
                'source' => 'HR. Bukhari & Muslim',
                'type' => 'hadith',
                'category' => 'hadits'
            ],

            // ULAMA - Quote tentang Keuangan
            [
                'content' => 'Kekayaan bukanlah tentang memiliki banyak harta, tetapi tentang merasa cukup dengan apa yang dimiliki.',
                'source' => 'Imam Al-Ghazali',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Harta adalah ujian. Jika kamu menggunakannya dengan benar, ia akan menjadi berkah. Jika tidak, ia akan menjadi bencana.',
                'source' => 'Imam Syafi\'i',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Jangan menjadi budak dunia, tetapi jadilah tuannya. Gunakan harta untuk kebaikan, bukan untuk kesombongan.',
                'source' => 'Umar bin Khattab',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Simpanlah hartamu dengan sedekah, karena sedekah adalah benteng dari bencana.',
                'source' => 'Ali bin Abi Thalib',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Rezeki yang halal adalah kunci keberkahan hidup. Jangan pernah mengejar harta dengan cara yang haram.',
                'source' => 'Imam Ahmad bin Hanbal',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Orang yang paling kaya adalah orang yang paling qana\'ah (merasa cukup).',
                'source' => 'Imam Malik',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Hutang adalah beban yang berat. Lunaskan hutangmu sebelum kamu tidur dengan tenang.',
                'source' => 'Hasan Al-Basri',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Jangan menunda sedekah. Karena kamu tidak tahu apakah besok kamu masih memiliki kesempatan.',
                'source' => 'Sufyan Ats-Tsauri',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Harta yang paling berharga adalah harta yang kamu infakkan di jalan Allah.',
                'source' => 'Abdullah bin Mubarak',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Jangan iri dengan kekayaan orang lain. Bersyukurlah dengan apa yang Allah berikan kepadamu.',
                'source' => 'Imam An-Nawawi',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Bekerjalah untuk duniamu seolah-olah kamu akan hidup selamanya, dan bekerjalah untuk akhiratmu seolah-olah kamu akan mati besok.',
                'source' => 'Ali bin Abi Thalib',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Kekayaan yang sejati adalah hati yang tenang dan jiwa yang damai.',
                'source' => 'Imam Al-Ghazali',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Jangan biarkan cinta dunia menguasai hatimu. Karena hati yang penuh dengan cinta dunia tidak akan ada tempat untuk cinta kepada Allah.',
                'source' => 'Imam Ibnu Qayyim',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Zakat bukan hanya kewajiban, tetapi juga investasi untuk akhirat.',
                'source' => 'Imam Syafi\'i',
                'type' => 'quote',
                'category' => 'ulama'
            ],
            [
                'content' => 'Harta yang kamu simpan untuk dirimu sendiri akan habis, tetapi harta yang kamu sedekahkan akan kekal di sisi Allah.',
                'source' => 'Umar bin Abdul Aziz',
                'type' => 'quote',
                'category' => 'ulama'
            ],

            // TIPS - Tips Keuangan Islami Praktis
            [
                'content' => 'Sisihkan 10% dari penghasilanmu untuk sedekah. Ini akan membawa berkah pada 90% sisanya.',
                'source' => 'Tips Keuangan Islami',
                'type' => 'quote',
                'category' => 'tips'
            ],
            [
                'content' => 'Buat anggaran bulanan dan patuhi. Disiplin adalah kunci kesuksesan finansial.',
                'source' => 'Tips Keuangan Islami',
                'type' => 'quote',
                'category' => 'tips'
            ],
            [
                'content' => 'Hindari hutang konsumtif. Jika terpaksa berhutang, pastikan untuk produktif dan segera lunasi.',
                'source' => 'Tips Keuangan Islami',
                'type' => 'quote',
                'category' => 'tips'
            ],
            [
                'content' => 'Investasi terbaik adalah investasi pada dirimu sendiri: pendidikan, kesehatan, dan skill.',
                'source' => 'Tips Keuangan Islami',
                'type' => 'quote',
                'category' => 'tips'
            ],
            [
                'content' => 'Simpan dana darurat minimal 3-6 bulan pengeluaran. Ini adalah bentuk ikhtiar menghadapi ketidakpastian.',
                'source' => 'Tips Keuangan Islami',
                'type' => 'quote',
                'category' => 'tips'
            ],
            [
                'content' => 'Jangan membeli sesuatu hanya karena diskon. Beli karena memang butuh.',
                'source' => 'Tips Keuangan Islami',
                'type' => 'quote',
                'category' => 'tips'
            ],
            [
                'content' => 'Catat setiap pengeluaran. Awareness adalah langkah pertama menuju kontrol finansial.',
                'source' => 'Tips Keuangan Islami',
                'type' => 'quote',
                'category' => 'tips'
            ],
            [
                'content' => 'Prioritaskan kebutuhan daripada keinginan. Bedakan antara "butuh" dan "ingin".',
                'source' => 'Tips Keuangan Islami',
                'type' => 'quote',
                'category' => 'tips'
            ],
            [
                'content' => 'Investasi halal adalah investasi yang membawa berkah. Hindari riba dan gharar.',
                'source' => 'Tips Keuangan Islami',
                'type' => 'quote',
                'category' => 'tips'
            ],
            [
                'content' => 'Ajarkan anak tentang keuangan sejak dini. Pendidikan finansial adalah warisan terbaik.',
                'source' => 'Tips Keuangan Islami',
                'type' => 'quote',
                'category' => 'tips'
            ],
        ];

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
