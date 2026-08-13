<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/berita', [SiteController::class, 'posts'])->name('posts.index');
Route::get('/berita/{slug}', [SiteController::class, 'post'])->name('posts.show');
Route::post('/berita/{slug}/komentar', [SiteController::class, 'storeComment'])->name('posts.comments.store');
Route::get('/pengumuman', [SiteController::class, 'announcements'])->name('announcements.index');
Route::get('/agenda', [SiteController::class, 'agendas'])->name('agendas.index');
Route::get('/galeri', [SiteController::class, 'gallery'])->name('gallery.index');
Route::get('/prestasi', [SiteController::class, 'achievements'])->name('achievements.index');
Route::get('/unduhan', [SiteController::class, 'downloads'])->name('downloads.index');
Route::get('/unduhan/{download}/file', [SiteController::class, 'downloadFile'])->name('downloads.file');
Route::get('/tenaga-pendidik', [SiteController::class, 'staff'])->name('staff.index');
Route::get('/struktur-organisasi', [SiteController::class, 'organization'])->name('organization.index');
Route::get('/short', [SiteController::class, 'shorts'])->name('shorts.index');
Route::get('/video', [SiteController::class, 'videos'])->name('videos.index');
Route::get('/layanan', [SiteController::class, 'layanan'])->name('layanan');
Route::get('/kontak', [SiteController::class, 'contact'])->name('contact');
Route::post('/kontak', [SiteController::class, 'contactStore'])->name('contact.store');
Route::get('/halaman/{slug}', [SiteController::class, 'page'])->name('pages.show');

Route::get('/manifest.webmanifest', function () {
    $site = \App\Models\SiteSetting::current();

    return response()->json([
        'name' => $site->school_name,
        'short_name' => 'MTsN 11',
        'description' => $site->tagline,
        'start_url' => '/',
        'display' => 'standalone',
        'background_color' => '#f4f7f5',
        'theme_color' => $site->primary_color ?: '#0a7a3e',
        'lang' => 'id',
        'icons' => array_values(array_filter([
            $site->logo ? [
                'src' => asset('storage/'.$site->logo),
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any',
            ] : null,
            $site->favicon ? [
                'src' => asset('storage/'.$site->favicon),
                'sizes' => '64x64',
                'type' => 'image/png',
            ] : null,
        ])),
    ], 200, ['Content-Type' => 'application/manifest+json']);
})->name('manifest');
