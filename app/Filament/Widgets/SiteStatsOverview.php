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
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SiteStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Statistik situs';

    protected ?string $description = 'Ringkasan performa konten dan aktivitas situs';

    protected function getStats(): array
    {
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
            Stat::make('Berita terbit', number_format($publishedPosts))
                ->description('Artikel yang sedang tayang')
                ->descriptionIcon(Heroicon::OutlinedNewspaper)
                ->color('success')
                ->url(PostResource::getUrl('index')),
            Stat::make('Total tayangan', number_format($totalViews))
                ->description('Akumulasi views berita')
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
}
