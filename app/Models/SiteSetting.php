<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'school_name',
        'tagline',
        'logo',
        'kemenag_logo',
        'favicon',
        'primary_color',
        'accent_color',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'hero_image_position',
        'hero_cta_label',
        'hero_cta_url',
        'headmaster_name',
        'headmaster_title',
        'headmaster_photo',
        'headmaster_quote',
        'accreditation_label',
        'accreditation_value',
        'accreditation_image',
        'npsn',
        'students_count',
        'teachers_count',
        'classes_count',
        'alumni_count',
        'founded_year',
        'address',
        'phone',
        'whatsapp_number',
        'email',
        'map_embed_url',
        'ppdb_url',
        'rdm_url',
        'kemenag_url',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'profile_video_url',
        'footer_text',
        'userway_account_id',
        'mascot_enabled',
        'mascot_theme',
        'mascot_message',
        'mascot_starts_on',
        'mascot_ends_on',
    ];

    protected function casts(): array
    {
        return [
            'mascot_enabled' => 'boolean',
            'mascot_starts_on' => 'date',
            'mascot_ends_on' => 'date',
        ];
    }

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
            'students_count' => 420,
            'teachers_count' => 32,
            'classes_count' => 15,
            'alumni_count' => 2000,
            'founded_year' => 2015,
            'address' => 'Kp. Sindanghurip RT 05/04 No. 21, Maniis, Cingambul, Majalengka 45467',
            'phone' => '(0233) 8319182',
            'whatsapp_number' => '6281234567890',
            'email' => 'mtsn11majalengka@gmail.com',
            'ppdb_url' => 'https://ppdb.mtsn11majalengka.sch.id/',
            'rdm_url' => 'https://rdm.mtsn11majalengka.sch.id/',
            'kemenag_url' => 'https://kemenag.go.id/',
            'profile_video_url' => null,
            'footer_text' => 'MTsN 11 Majalengka — Naungan Kementerian Agama Republik Indonesia',
        ]);
    }

    public function whatsappLink(?string $message = null): ?string
    {
        if (blank($this->whatsapp_number)) {
            return null;
        }

        $number = preg_replace('/\D+/', '', $this->whatsapp_number);
        $text = $message ?: 'Assalamu\'alaikum, saya ingin bertanya tentang MTsN 11 Majalengka.';

        return 'https://wa.me/'.$number.'?text='.urlencode($text);
    }

    public function youtubeEmbedUrl(): ?string
    {
        if (blank($this->profile_video_url)) {
            return null;
        }

        if (str_contains($this->profile_video_url, 'youtube.com/embed/')) {
            return $this->profile_video_url;
        }

        if (preg_match('/(?:youtu\.be\/|v=)([^&]+)/', $this->profile_video_url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        return $this->profile_video_url;
    }

    /**
     * CSS object-position / transform-origin untuk fokus gambar hero.
     */
    public function heroImageFocus(): string
    {
        $position = trim((string) $this->hero_image_position);

        return $position !== '' ? $position : '50% 40%';
    }

    public function mascotCustomLines(): array
    {
        if (blank($this->mascot_message)) {
            return [];
        }

        $today = now()->startOfDay();

        if ($this->mascot_starts_on && $today->lt($this->mascot_starts_on->startOfDay())) {
            return [];
        }

        if ($this->mascot_ends_on && $today->gt($this->mascot_ends_on->startOfDay())) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', (string) $this->mascot_message))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
