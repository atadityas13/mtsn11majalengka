<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'school_name',
        'tagline',
        'logo',
        'favicon',
        'primary_color',
        'accent_color',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'hero_cta_label',
        'hero_cta_url',
        'headmaster_name',
        'headmaster_title',
        'headmaster_photo',
        'headmaster_quote',
        'accreditation_label',
        'accreditation_value',
        'npsn',
        'address',
        'phone',
        'email',
        'map_embed_url',
        'ppdb_url',
        'rdm_url',
        'kemenag_url',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'footer_text',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'school_name' => 'MTsN 11 Majalengka',
            'tagline' => 'Madrasah Tsanawiyah Negeri — Membentuk Generasi Beriman, Berilmu, dan Berakhlak',
            'primary_color' => '#0a7a3e',
            'accent_color' => '#d4a017',
            'hero_title' => 'MTsN 11 Majalengka',
            'hero_subtitle' => 'Madrasah negeri di bawah Kementerian Agama RI yang berkomitmen mencetak generasi Qur\'ani, berprestasi, dan berkarakter.',
            'hero_cta_label' => 'Info PPDB',
            'hero_cta_url' => 'https://ppdb.mtsn11majalengka.sch.id/',
            'headmaster_name' => 'H. Jajang Gunawan, S.Ag., M.Pd.I',
            'headmaster_title' => 'Kepala Madrasah',
            'headmaster_quote' => 'MTsN 11 Majalengka berkomitmen mendampingi, melayani, dan memberikan yang terbaik bagi peserta didik.',
            'accreditation_label' => 'Akreditasi',
            'accreditation_value' => 'A',
            'npsn' => '20278893',
            'address' => 'Kp. Sindanghurip RT 05/04 No. 21, Maniis, Cingambul, Majalengka 45467',
            'phone' => '(0233) 8319182',
            'email' => 'mtsn11majalengka@gmail.com',
            'ppdb_url' => 'https://ppdb.mtsn11majalengka.sch.id/',
            'rdm_url' => 'https://rdm.mtsn11majalengka.sch.id/',
            'kemenag_url' => 'https://kemenag.go.id/',
            'footer_text' => 'MTsN 11 Majalengka — Naungan Kementerian Agama Republik Indonesia',
        ]);
    }
}
