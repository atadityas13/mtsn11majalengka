<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\OrganizationNode;
use App\Models\Page;
use App\Models\Post;
use App\Models\ServiceLink;
use App\Models\SiteSetting;
use App\Models\StaffMember;
use App\Models\User;
use App\Models\Video;
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
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        SiteSetting::current();

        $headerMenus = [
            ['label' => 'Beranda', 'url' => '/', 'sort_order' => 1, 'parent' => null],
            ['label' => 'Berita', 'url' => '/berita', 'sort_order' => 2, 'parent' => null],
            ['label' => 'Pengumuman', 'url' => '/pengumuman', 'sort_order' => 3, 'parent' => null],
            ['label' => 'Agenda', 'url' => '/agenda', 'sort_order' => 4, 'parent' => null],
            ['label' => 'Prestasi', 'url' => '/prestasi', 'sort_order' => 5, 'parent' => null],
            ['label' => 'Galeri', 'url' => '/galeri', 'sort_order' => 6, 'parent' => null],
            ['label' => 'Short', 'url' => '/short', 'sort_order' => 7, 'parent' => null],
            ['label' => 'Video', 'url' => '/video', 'sort_order' => 8, 'parent' => null],
            ['label' => 'Profil', 'url' => '/halaman/profil', 'sort_order' => 9, 'parent' => null],
            ['label' => 'Struktur Organisasi', 'url' => '/struktur-organisasi', 'sort_order' => 1, 'parent' => 'Profil'],
            ['label' => 'Guru & Tendik', 'url' => '/tenaga-pendidik', 'sort_order' => 2, 'parent' => 'Profil'],
            ['label' => 'Unduhan', 'url' => '/unduhan', 'sort_order' => 10, 'parent' => null],
            ['label' => 'Layanan', 'url' => '/layanan', 'sort_order' => 11, 'parent' => null],
            ['label' => 'Kontak', 'url' => '/kontak', 'sort_order' => 12, 'parent' => null],
        ];

        foreach ($headerMenus as $menu) {
            $parentLabel = $menu['parent'] ?? null;
            unset($menu['parent']);

            $parentId = null;
            if ($parentLabel) {
                $parentId = MenuItem::query()
                    ->where('location', 'header')
                    ->where('label', $parentLabel)
                    ->whereNull('parent_id')
                    ->value('id');
            }

            MenuItem::query()->updateOrCreate(
                ['label' => $menu['label'], 'location' => 'header'],
                [
                    ...$menu,
                    'location' => 'header',
                    'parent_id' => $parentId,
                    'is_visible' => true,
                    'open_in_new_tab' => false,
                ]
            );
        }

        $footerMenus = [
            ['label' => 'Profil', 'url' => '/halaman/profil', 'sort_order' => 1],
            ['label' => 'Struktur Organisasi', 'url' => '/struktur-organisasi', 'sort_order' => 2],
            ['label' => 'Guru & Tendik', 'url' => '/tenaga-pendidik', 'sort_order' => 3],
            ['label' => 'Unduhan', 'url' => '/unduhan', 'sort_order' => 4],
            ['label' => 'Info PPDB', 'url' => '/halaman/ppdb', 'sort_order' => 5],
            ['label' => 'Kontak', 'url' => '/kontak', 'sort_order' => 6],
        ];

        foreach ($footerMenus as $menu) {
            MenuItem::query()->updateOrCreate(
                ['label' => $menu['label'], 'location' => 'footer'],
                [...$menu, 'location' => 'footer', 'is_visible' => true, 'open_in_new_tab' => false]
            );
        }

        $settings = SiteSetting::current();
        $serviceLinks = [
            [
                'label' => 'PPDB Online',
                'url' => $settings->ppdb_url ?: 'https://ppdb.mtsn11majalengka.sch.id/',
                'description' => 'Pendaftaran peserta didik baru',
                'sort_order' => 1,
                'open_in_new_tab' => true,
            ],
            [
                'label' => 'Rapor Digital',
                'url' => $settings->rdm_url ?: 'https://rdm.mtsn11majalengka.sch.id/',
                'description' => 'Portal RDM madrasah',
                'sort_order' => 2,
                'open_in_new_tab' => true,
            ],
            [
                'label' => 'Kemenag RI',
                'url' => $settings->kemenag_url ?: 'https://kemenag.go.id/',
                'description' => 'Portal Kementerian Agama',
                'sort_order' => 3,
                'open_in_new_tab' => true,
            ],
            [
                'label' => 'Kontak',
                'url' => '/kontak',
                'description' => 'Alamat & saluran komunikasi',
                'sort_order' => 4,
                'open_in_new_tab' => false,
            ],
        ];

        foreach ($serviceLinks as $link) {
            ServiceLink::query()->updateOrCreate(
                ['label' => $link['label']],
                [...$link, 'is_visible' => true]
            );
        }

        $kegiatan = Category::query()->updateOrCreate(
            ['slug' => 'kegiatan'],
            ['name' => 'Kegiatan', 'color' => '#0a7a3e', 'is_active' => true]
        );
        $keagamaan = Category::query()->updateOrCreate(
            ['slug' => 'keagamaan'],
            ['name' => 'Keagamaan', 'color' => '#d4a017', 'is_active' => true]
        );
        $prestasiCat = Category::query()->updateOrCreate(
            ['slug' => 'prestasi'],
            ['name' => 'Prestasi', 'color' => '#065c2e', 'is_active' => true]
        );

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
                'category_id' => $kegiatan->id,
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
                'category_id' => $keagamaan->id,
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
                'category_id' => $prestasiCat->id,
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

        Achievement::query()->updateOrCreate(
            ['title' => 'Juara 1 Lomba Voli Putra Kecamatan'],
            [
                'level' => 'Kecamatan',
                'winner_name' => 'Tim Voli MTsN 11 Majalengka',
                'achieved_on' => now()->subMonths(2)->toDateString(),
                'description' => 'Tim voli putra meraih juara 1 pada ajang tingkat kecamatan.',
                'sort_order' => 1,
                'is_published' => true,
            ]
        );

        Achievement::query()->updateOrCreate(
            ['title' => 'Apresiasi Jambore Ranting Cingambul'],
            [
                'level' => 'Kecamatan',
                'winner_name' => 'Pramuka MTsN 11',
                'achieved_on' => now()->subMonths(4)->toDateString(),
                'description' => 'Prestasi kepramukaan tingkat ranting.',
                'sort_order' => 2,
                'is_published' => true,
            ]
        );

        StaffMember::query()->updateOrCreate(
            ['name' => 'H. Jajang Gunawan, S.Ag., M.Pd.I'],
            [
                'role' => 'Kepala Madrasah',
                'sort_order' => 1,
                'is_published' => true,
            ]
        );

        StaffMember::query()->updateOrCreate(
            ['name' => 'Contoh Guru Mapel'],
            [
                'role' => 'Guru Mapel',
                'sort_order' => 2,
                'is_published' => true,
            ]
        );

        $settings = SiteSetting::current();

        $seedNode = function (string $slug, array $attrs, ?string $parentSlug = null) use (&$seedNode): OrganizationNode {
            $parentId = $parentSlug
                ? OrganizationNode::query()->where('slug', $parentSlug)->value('id')
                : null;

            return OrganizationNode::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    ...$attrs,
                    'slug' => $slug,
                    'parent_id' => $parentId,
                    'is_published' => true,
                ]
            );
        };

        $seedNode('komite-madrasah', [
            'title' => 'Komite Madrasah',
            'name' => null,
            'lane' => 'peer_top',
            'sort_order' => 1,
            'description' => 'Mitra masyarakat yang mendukung kebijakan dan pengembangan madrasah.',
        ]);

        $seedNode('kepala-madrasah', [
            'title' => 'Kepala Madrasah / Plt. Kamad',
            'name' => $settings->headmaster_name,
            'lane' => 'peer_top',
            'sort_order' => 2,
            'description' => 'Pimpinan satuan pendidikan; educator, manager, administrator, dan supervisor.',
        ]);

        $seedNode('kaur-tata-usaha', [
            'title' => 'Kepala Urusan Tata Usaha',
            'lane' => 'line',
            'sort_order' => 10,
            'description' => 'Mengelola ketatausahaan, administrasi, dan staf pendukung.',
        ], 'kepala-madrasah');

        $seedNode('waka-kurikulum', [
            'title' => 'Waka Kurikulum',
            'lane' => 'line',
            'sort_order' => 20,
            'description' => 'Mengelola kurikulum, jadwal, perangkat pembelajaran, dan evaluasi.',
        ], 'kepala-madrasah');

        $seedNode('waka-kesiswaan', [
            'title' => 'Waka Kesiswaan',
            'lane' => 'line',
            'sort_order' => 21,
            'description' => 'Pembinaan kesiswaan, disiplin, OSIM, dan kegiatan siswa.',
        ], 'kepala-madrasah');

        $seedNode('waka-sarpras', [
            'title' => 'Waka Sarana & Prasarana',
            'lane' => 'line',
            'sort_order' => 22,
            'description' => 'Pengadaan, pemeliharaan, dan pengelolaan sarana prasarana.',
        ], 'kepala-madrasah');

        $seedNode('waka-humas', [
            'title' => 'Waka Humas',
            'lane' => 'line',
            'sort_order' => 23,
            'description' => 'Hubungan masyarakat, publikasi, dan kemitraan eksternal.',
        ], 'kepala-madrasah');

        $seedNode('bendahara', [
            'title' => 'Bendahara',
            'lane' => 'staff',
            'sort_order' => 30,
            'description' => 'Pengelolaan keuangan madrasah di bawah Kaur Tata Usaha.',
        ], 'kaur-tata-usaha');

        $seedNode('staf-tu', [
            'title' => 'Staf Tata Usaha',
            'lane' => 'staff',
            'sort_order' => 31,
            'description' => 'Pelayanan administrasi perkantoran dan kearsipan.',
        ], 'kaur-tata-usaha');

        $seedNode('kepala-laboratorium', [
            'title' => 'Kepala Laboratorium',
            'lane' => 'staff',
            'sort_order' => 40,
            'description' => 'Pengelolaan laboratorium pembelajaran (di bawah Waka Kurikulum).',
        ], 'waka-kurikulum');

        $seedNode('kepala-perpustakaan', [
            'title' => 'Kepala Perpustakaan',
            'lane' => 'staff',
            'sort_order' => 41,
            'description' => 'Pengelolaan perpustakaan dan literasi (di bawah Waka Kurikulum).',
        ], 'waka-kurikulum');

        $seedNode('kepala-asrama', [
            'title' => 'Kepala Asrama',
            'lane' => 'staff',
            'sort_order' => 42,
            'description' => 'Pembinaan santri asrama/boarding (di bawah Waka Kesiswaan).',
        ], 'waka-kesiswaan');

        $seedNode('guru-wali-kelas', [
            'title' => 'Guru & Wali Kelas',
            'name' => 'Seluruh tenaga pendidik',
            'lane' => 'collective',
            'sort_order' => 50,
            'description' => 'Melaksanakan pembelajaran dan pembimbingan di bawah koordinasi seluruh Waka.',
        ], 'kepala-madrasah');

        Video::query()->updateOrCreate(
            ['slug' => 'contoh-short-kegiatan-madrasah'],
            [
                'title' => 'Contoh Short Kegiatan Madrasah',
                'type' => 'short',
                'video_url' => 'https://www.youtube.com/shorts/aqz-KE-bpKQ',
                'description' => 'Cuplikan singkat kegiatan pembelajaran dan keagamaan di madrasah.',
                'sort_order' => 1,
                'is_published' => true,
                'published_at' => now()->subDay(),
            ]
        );

        Video::query()->updateOrCreate(
            ['slug' => 'contoh-video-profil'],
            [
                'title' => 'Contoh Video Profil',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
                'description' => 'Video dokumentasi profil dan kegiatan madrasah.',
                'sort_order' => 1,
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ]
        );
    }
}
