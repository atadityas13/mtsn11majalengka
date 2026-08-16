<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class AccessibilityStatementSeeder extends Seeder
{
    public function run(): void
    {
        $body = <<<'HTML'
<p><strong>MTsN 11 Majalengka</strong> berupaya memastikan bahwa layanan digitalnya dapat diakses oleh penyandang disabilitas. <strong>MTsN 11 Majalengka</strong> telah menginvestasikan sumber daya yang signifikan untuk membantu memastikan akses bagi semua pengguna, termasuk penyandang disabilitas, dengan keyakinan kuat bahwa setiap orang berhak untuk hidup dengan bermartabat, setara, nyaman, dan mandiri.</p>
<p>Aksesibilitas digital bukan hanya target yang ingin kita capai, tetapi juga komitmen berkelanjutan yang kita junjung tinggi dalam setiap aspek penyampaian layanan digital kita. Kami menyadari bahwa hal ini membutuhkan pemantauan, peningkatan, dan adaptasi yang berkelanjutan. Pada bagian di bawah ini, kami menguraikan berbagai upaya yang telah dilakukan untuk mengatasi kemajuan kami dalam memastikan aksesibilitas digital bagi semua orang.</p>
<p>Elemen kunci dari inisiatif aksesibilitas kami adalah keterlibatan kami dengan organisasi konsultan aksesibilitas pihak ketiga, UserWay, untuk membantu menyelaraskan situs web kami dengan standar aksesibilitas. Kami menggunakan metode dan alat aksesibilitas standar industri untuk mencapai hal ini, sesuai dengan versi <a href="https://www.w3.org/TR/WCAG21/" title="Pedoman Aksesibilitas Konten Web" rel="noopener nofollow" target="_blank">Web Content Accessibility Guidelines (WCAG)</a> yang relevan sebagaimana ditetapkan oleh World Wide Web Consortium (W3C), organisasi penetapan standar internasional utama internet.</p>
<h2>Silakan Hubungi Kami</h2>
<p>Kami hadir untuk membantu Anda dan membuat pengalaman Anda semulus dan seinklusif mungkin. Jika Anda mengalami masalah aksesibilitas digital dengan produk atau layanan kami, atau membutuhkan informasi terkait aksesibilitas digital, kami ingin mendengar dari Anda! Selain itu, kami menghargai dan menyambut umpan balik, dan kami selalu siap menerima saran untuk meningkatkan upaya aksesibilitas digital kami.</p>
<p>Informasi Kontak:</p>
<ul>
<li>MTsN 11 Majalengka</li>
<li>Email: <a href="mailto:mtsn11majalengka@gmail.com">mtsn11majalengka@gmail.com</a></li>
</ul>
<p>Kami sangat memperhatikan aksesibilitas dan akan segera menghubungi Anda kembali.</p>
HTML;

        Page::query()->updateOrCreate(
            ['slug' => 'aksesibilitas'],
            [
                'title' => 'Pernyataan Aksesibilitas',
                'subtitle' => 'Komitmen MTsN 11 Majalengka terhadap aksesibilitas digital',
                'body' => $body,
                'is_published' => true,
            ],
        );
    }
}
