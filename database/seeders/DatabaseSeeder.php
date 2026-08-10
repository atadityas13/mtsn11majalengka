<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@mtsn11majalengka.sch.id'],
            [
                'name' => 'Admin MTsN 11',
                'password' => Hash::make('password'),
            ]
        );

        SiteSetting::current();

        $headerMenus = [
            ['label' => 'Beranda', 'url' => '/', 'sort_order' => 1],
            ['label' => 'Berita', 'url' => '/berita', 'sort_order' => 2],
            ['label' => 'Pengumuman', 'url' => '/pengumuman', 'sort_order' => 3],
            ['label' => 'Agenda', 'url' => '/agenda', 'sort_order' => 4],
            ['label' => 'Galeri', 'url' => '/galeri', 'sort_order' => 5],
            ['label' => 'Profil', 'url' => '/halaman/profil', 'sort_order' => 6],
            ['label' => 'Akademik', 'url' => '/halaman/akademik', 'sort_order' => 7],
            ['label' => 'Layanan', 'url' => '/layanan', 'sort_order' => 8],
            ['label' => 'Kontak', 'url' => '/kontak', 'sort_order' => 9],
        ];

        foreach ($headerMenus as $menu) {
            MenuItem::query()->updateOrCreate(
                ['label' => $menu['label'], 'location' => 'header'],
                [...$menu, 'location' => 'header', 'is_visible' => true, 'open_in_new_tab' => false]
            );
        }

        $footerMenus = [
            ['label' => 'Profil', 'url' => '/halaman/profil', 'sort_order' => 1],
            ['label' => 'Akademik', 'url' => '/halaman/akademik', 'sort_order' => 2],
            ['label' => 'Info PPDB', 'url' => '/halaman/ppdb', 'sort_order' => 3],
            ['label' => 'Kontak', 'url' => '/kontak', 'sort_order' => 4],
        ];

        foreach ($footerMenus as $menu) {
            MenuItem::query()->updateOrCreate(
                ['label' => $menu['label'], 'location' => 'footer'],
                [...$menu, 'location' => 'footer', 'is_visible' => true, 'open_in_new_tab' => false]
            );
        }

        Page::query()->updateOrCreate(
            ['slug' => 'profil'],
            [
                'title' => 'Profil Madrasah',
                'subtitle' => 'Mengenal MTsN 11 Majalengka',
                'body' => '<p>MTsN 11 Majalengka adalah Madrasah Tsanawiyah Negeri di Kecamatan Cingambul, Kabupaten Majalengka, Jawa Barat, di bawah naungan Kementerian Agama RI.</p><p>Dengan akreditasi A (NPSN 20278893), madrasah berkomitmen membentuk peserta didik yang beriman, berilmu, dan berakhlak mulia melalui pembelajaran akademik dan kegiatan keagamaan seperti qiro\'ah serta kader dakwah.</p>',
                'is_published' => true,
            ]
        );

        Page::query()->updateOrCreate(
            ['slug' => 'akademik'],
            [
                'title' => 'Akademik & Kesiswaan',
                'subtitle' => 'Kurikulum, ekstrakurikuler, dan pembinaan karakter',
                'body' => '<p>Pembelajaran di MTsN 11 Majalengka mengintegrasikan kurikulum nasional dengan nilai-nilai keislaman.</p><p>Kegiatan kesiswaan mencakup ekstrakurikuler olahraga (termasuk voli), kepramukaan/jambore, serta program keagamaan rutin seperti qiro\'ah dan pelatihan kader dakwah.</p>',
                'is_published' => true,
            ]
        );

        Page::query()->updateOrCreate(
            ['slug' => 'ppdb'],
            [
                'title' => 'Informasi PPDB',
                'subtitle' => 'Penerimaan Peserta Didik Baru',
                'body' => '<p>Pendaftaran peserta didik baru MTsN 11 Majalengka tersedia melalui portal PPDB online, termasuk jalur Reguler dan Baitul Ilmi Boarding School.</p><p>Silakan akses tombol PPDB pada menu situs atau kunjungi portal resmi untuk mengisi formulir dan mengikuti tahapan verifikasi.</p>',
                'is_published' => true,
            ]
        );

        Post::query()->updateOrCreate(
            ['slug' => 'upacara-bendera-semarakkan-awal-pekan'],
            [
                'title' => 'Upacara Bendera Semarakkan Awal Pekan, Tingkatkan Nasionalisme',
                'excerpt' => 'Upacara bendera rutin mingguan dilaksanakan di MTsN 11 Majalengka bersama seluruh warga madrasah.',
                'body' => '<p>Upacara bendera rutin mingguan kembali dilaksanakan di MTs Negeri 11 Majalengka. Kegiatan diikuti oleh seluruh siswa, guru, dan tenaga kependidikan sebagai bagian dari pembinaan karakter dan nasionalisme.</p>',
                'author_name' => 'Humas MTsN 11',
                'published_at' => now()->subDays(3),
                'is_published' => true,
            ]
        );

        Post::query()->updateOrCreate(
            ['slug' => 'kegiatan-qiroah-rutin-generasi-qurani'],
            [
                'title' => 'Bentuk Generasi Qur\'ani, MTsN 11 Gelar Qiro\'ah Rutin',
                'excerpt' => 'Program pembelajaran Al-Qur\'an digelar secara konsisten setiap pekan untuk memperkuat literasi Qur\'ani siswa.',
                'body' => '<p>Dalam upaya mencetak generasi muda yang Qur\'ani, MTsN 11 Majalengka secara konsisten menggelar program pembelajaran Al-Qur\'an atau qiro\'ah sebagai pembiasaan ibadah dan literasi keagamaan.</p>',
                'author_name' => 'Humas MTsN 11',
                'published_at' => now()->subDays(5),
                'is_published' => true,
            ]
        );

        Post::query()->updateOrCreate(
            ['slug' => 'apresiasi-prestasi-jambore-cingambul'],
            [
                'title' => 'Kepala Madrasah Apresiasi Prestasi Siswa di Jambore Kecamatan',
                'excerpt' => 'Kepala Madrasah memberikan apresiasi kepada siswa-siswi berprestasi pada jambore tingkat ranting Kecamatan Cingambul.',
                'body' => '<p>Kepala Madrasah MTsN 11 Majalengka memberikan apresiasi kepada para siswa yang menorehkan prestasi pada kegiatan jambore tingkat ranting Kecamatan Cingambul.</p>',
                'author_name' => 'Humas MTsN 11',
                'published_at' => now()->subDays(7),
                'is_published' => true,
            ]
        );

        Announcement::query()->updateOrCreate(
            ['title' => 'Informasi PPDB Tahun Ajaran Baru'],
            [
                'body' => '<p>Informasi penerimaan peserta didik baru dapat diakses melalui portal PPDB resmi madrasah. Pastikan berkas pendaftaran lengkap sebelum batas waktu.</p>',
                'published_on' => now()->toDateString(),
                'is_pinned' => true,
                'is_published' => true,
            ]
        );

        Agenda::query()->updateOrCreate(
            ['title' => 'Rapat Koordinasi Guru dan Tendik'],
            [
                'description' => 'Koordinasi program pembelajaran dan kegiatan madrasah minggu berjalan.',
                'location' => 'Ruang Guru MTsN 11 Majalengka',
                'starts_at' => now()->addDays(2)->setTime(9, 0),
                'ends_at' => now()->addDays(2)->setTime(11, 0),
                'is_published' => true,
            ]
        );
    }
}
