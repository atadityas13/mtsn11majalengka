<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Models\Post;
use App\Services\WebPushService;
use Illuminate\Console\Command;

class DispatchPendingWebPush extends Command
{
    protected $signature = 'webpush:dispatch-pending';

    protected $description = 'Kirim push untuk berita/pengumuman yang sudah tayang tapi belum dinotifikasi';

    public function handle(WebPushService $webPush): int
    {
        if (! $webPush->isConfigured()) {
            $this->warn('VAPID keys belum dikonfigurasi.');

            return self::SUCCESS;
        }

        $posts = Post::query()
            ->published()
            ->whereNull('push_sent_at')
            ->orderBy('id')
            ->get();

        foreach ($posts as $post) {
            $webPush->notifyPost($post);
            $this->line('Post #'.$post->id);
        }

        $announcements = Announcement::query()
            ->published()
            ->whereNull('push_sent_at')
            ->orderBy('id')
            ->get();

        foreach ($announcements as $announcement) {
            $webPush->notifyAnnouncement($announcement);
            $this->line('Announcement #'.$announcement->id);
        }

        $this->info('Selesai: '.$posts->count().' berita, '.$announcements->count().' pengumuman.');

        return self::SUCCESS;
    }
}
