<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Agendas\AgendaResource;
use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Filament\Resources\Comments\CommentResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Posts\PostResource;
use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\PushSubscription;
use App\Models\SiteVisit;
use App\Models\User;
use App\Services\WebPushService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SiteStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Statistik situs';

    protected ?string $description = 'Ringkasan pengunjung, konten, dan aktivitas situs';

    protected function getStats(): array
    {
        try {
            $visits = SiteVisit::stats();
        } catch (\Throwable) {
            $visits = [
                'today_visitors' => 0,
                'today_page_views' => 0,
                'total_visitors' => 0,
                'total_page_views' => 0,
            ];
        }

        $publishedPosts = Post::query()->published()->count();
        $totalViews = (int) Post::query()->sum('views_count');
        $visibleComments = Comment::query()->where('is_approved', true)->count();
        $unreadMessages = ContactMessage::query()->whereNull('read_at')->count();
        $upcomingAgendas = Agenda::query()
            ->published()
            ->where('starts_at', '>=', now())
            ->count();
        $activeAnnouncements = Announcement::query()->published()->count();

        $stats = [
            Stat::make('Pengunjung hari ini', number_format($visits['today_visitors']))
                ->description('Sesi unik hari ini')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('success'),
            Stat::make('Tayangan hari ini', number_format($visits['today_page_views']))
                ->description('Halaman yang dibuka hari ini')
                ->descriptionIcon(Heroicon::OutlinedCursorArrowRays)
                ->color('primary'),
            Stat::make('Total pengunjung', number_format($visits['total_visitors']))
                ->description('Akumulasi sesi unik')
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->color('info'),
            Stat::make('Total tayangan situs', number_format($visits['total_page_views']))
                ->description('Akumulasi halaman dibuka')
                ->descriptionIcon(Heroicon::OutlinedGlobeAlt)
                ->color('primary'),
            Stat::make('Berita terbit', number_format($publishedPosts))
                ->description('Artikel yang sedang tayang')
                ->descriptionIcon(Heroicon::OutlinedNewspaper)
                ->color('success')
                ->url(PostResource::getUrl('index')),
            Stat::make('Tayangan berita', number_format($totalViews))
                ->description('Akumulasi views detail berita')
                ->descriptionIcon(Heroicon::OutlinedEye)
                ->color('primary'),
            Stat::make('Komentar tayang', number_format($visibleComments))
                ->description('Ditampilkan di situs')
                ->descriptionIcon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('success')
                ->url(CommentResource::getUrl('index')),
            Stat::make('Pesan kontak baru', number_format($unreadMessages))
                ->description('Belum dibaca')
                ->descriptionIcon(Heroicon::OutlinedInbox)
                ->color($unreadMessages > 0 ? 'danger' : 'gray')
                ->url(ContactMessageResource::getUrl('index')),
            Stat::make('Agenda mendatang', number_format($upcomingAgendas))
                ->description('Kegiatan terjadwal')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('info')
                ->url(AgendaResource::getUrl('index')),
            Stat::make('Pengumuman aktif', number_format($activeAnnouncements))
                ->description('Sedang dipublikasikan')
                ->descriptionIcon(Heroicon::OutlinedMegaphone)
                ->color('success')
                ->url(AnnouncementResource::getUrl('index')),
            Stat::make('Subscriber notifikasi', number_format($this->subscriberCount()))
                ->description(app(WebPushService::class)->isConfigured()
                    ? 'Browser yang mengikuti kabar situs'
                    : 'VAPID belum dikonfigurasi di .env')
                ->descriptionIcon(Heroicon::OutlinedBellAlert)
                ->color('warning'),
        ];

        $user = Auth::user();
        if ($user instanceof User && $user->isSuperAdmin()) {
            $stats[] = Stat::make('Pengguna admin', number_format(User::query()->where('is_active', true)->count()))
                ->description('Akun aktif')
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->color('gray')
                ->url(\App\Filament\Resources\Users\UserResource::getUrl('index'));
        }

        return $stats;
    }

    protected function subscriberCount(): int
    {
        try {
            return PushSubscription::query()->count();
        } catch (\Throwable) {
            return 0;
        }
    }
}
