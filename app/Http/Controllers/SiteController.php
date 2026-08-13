<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Download;
use App\Models\GalleryItem;
use App\Models\OrganizationNode;
use App\Models\Page;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\StaffMember;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiteController extends Controller
{
    public function home(): View
    {
        return view('site.home', [
            'posts' => Post::published()->with('category')->latest('published_at')->take(5)->get(),
            'announcements' => Announcement::published()->orderByDesc('is_pinned')->latest('published_on')->take(5)->get(),
            'agendas' => Agenda::published()->where('starts_at', '>=', now()->subDay())->orderBy('starts_at')->take(4)->get(),
            'gallery' => GalleryItem::published()->orderBy('sort_order')->take(6)->get(),
            'achievements' => Achievement::published()->orderBy('sort_order')->latest('achieved_on')->take(6)->get(),
            'shorts' => Video::published()->shorts()->orderBy('sort_order')->latest('published_at')->take(8)->get(),
            'homeVideos' => Video::published()->longVideos()->orderBy('sort_order')->latest('published_at')->take(3)->get(),
        ]);
    }

    public function posts(Request $request): View
    {
        $query = Post::published()->with('category')->latest('published_at');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($categorySlug = $request->string('kategori')->trim()->toString()) {
            $query->whereHas('category', fn ($builder) => $builder->where('slug', $categorySlug));
        }

        return view('site.posts.index', [
            'posts' => $query->paginate(9)->withQueryString(),
            'categories' => Category::active()->orderBy('name')->get(),
            'search' => $search ?? '',
            'activeCategory' => $categorySlug ?? '',
        ]);
    }

    public function post(string $slug): View
    {
        $post = Post::published()->with('category')->where('slug', $slug)->firstOrFail();

        return view('site.posts.show', [
            'post' => $post,
            'related' => Post::published()
                ->with('category')
                ->where('id', '!=', $post->id)
                ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
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

    public function agendas(Request $request): View
    {
        $month = (int) $request->input('bulan', now()->month);
        $year = (int) $request->input('tahun', now()->year);
        $cursor = Carbon::create($year, $month, 1)->startOfMonth();

        $monthAgendas = Agenda::published()
            ->whereBetween('starts_at', [$cursor->copy()->startOfMonth(), $cursor->copy()->endOfMonth()])
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn (Agenda $agenda) => $agenda->starts_at->format('Y-m-d'));

        return view('site.agendas', [
            'agendas' => Agenda::published()->orderBy('starts_at')->paginate(12)->withQueryString(),
            'cursor' => $cursor,
            'monthAgendas' => $monthAgendas,
            'prev' => $cursor->copy()->subMonth(),
            'next' => $cursor->copy()->addMonth(),
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

    public function contactStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::query()->create($data);

        return back()->with('success', 'Pesan Anda berhasil dikirim. Terima kasih.');
    }

    public function layanan(): View
    {
        return view('site.layanan');
    }

    public function downloads(): View
    {
        return view('site.downloads', [
            'downloads' => Download::published()->latest()->paginate(12),
        ]);
    }

    public function downloadFile(Download $download): StreamedResponse
    {
        abort_unless($download->is_published && $download->file_path, 404);
        abort_unless(Storage::disk('public')->exists($download->file_path), 404);

        $download->increment('download_count');

        return Storage::disk('public')->download($download->file_path, basename($download->file_path));
    }

    public function staff(): View
    {
        return view('site.staff', [
            'staff' => StaffMember::published()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function organization(): View
    {
        $nodes = OrganizationNode::published()
            ->orderBy('sort_order')
            ->get()
            ->keyBy('slug');

        return view('site.organization', [
            'nodes' => $nodes,
            'cards' => OrganizationNode::published()->orderBy('sort_order')->get(),
        ]);
    }

    public function shorts(): View
    {
        return view('site.shorts', [
            'shorts' => Video::published()->shorts()->orderBy('sort_order')->latest('published_at')->get(),
        ]);
    }

    public function videos(): View
    {
        return view('site.videos', [
            'videos' => Video::published()->longVideos()->orderBy('sort_order')->latest('published_at')->paginate(12),
            'shorts' => Video::published()->shorts()->orderBy('sort_order')->latest('published_at')->take(8)->get(),
        ]);
    }

    public function achievements(): View
    {
        return view('site.achievements', [
            'achievements' => Achievement::published()->orderBy('sort_order')->latest('achieved_on')->paginate(12),
        ]);
    }
}
