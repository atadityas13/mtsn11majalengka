<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\GalleryItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\SiteSetting;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        return view('site.home', [
            'posts' => Post::published()->latest('published_at')->take(6)->get(),
            'announcements' => Announcement::published()->orderByDesc('is_pinned')->latest('published_on')->take(5)->get(),
            'agendas' => Agenda::published()->where('starts_at', '>=', now()->subDay())->orderBy('starts_at')->take(4)->get(),
            'gallery' => GalleryItem::published()->orderBy('sort_order')->take(6)->get(),
        ]);
    }

    public function posts(): View
    {
        return view('site.posts.index', [
            'posts' => Post::published()->latest('published_at')->paginate(9),
        ]);
    }

    public function post(string $slug): View
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        return view('site.posts.show', [
            'post' => $post,
            'related' => Post::published()
                ->where('id', '!=', $post->id)
                ->latest('published_at')
                ->take(3)
                ->get(),
        ]);
    }

    public function announcements(): View
    {
        return view('site.announcements', [
            'announcements' => Announcement::published()
                ->orderByDesc('is_pinned')
                ->latest('published_on')
                ->paginate(12),
        ]);
    }

    public function agendas(): View
    {
        return view('site.agendas', [
            'agendas' => Agenda::published()->orderBy('starts_at')->paginate(12),
        ]);
    }

    public function gallery(): View
    {
        return view('site.gallery', [
            'items' => GalleryItem::published()->orderBy('sort_order')->paginate(12),
        ]);
    }

    public function page(string $slug): View
    {
        $page = Page::published()->where('slug', $slug)->firstOrFail();

        return view('site.page', compact('page'));
    }

    public function contact(): View
    {
        return view('site.contact', [
            'settings' => SiteSetting::current(),
        ]);
    }

    public function layanan(): View
    {
        return view('site.layanan', [
            'settings' => SiteSetting::current(),
        ]);
    }
}
