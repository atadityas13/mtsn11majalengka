<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/berita', [SiteController::class, 'posts'])->name('posts.index');
Route::get('/berita/{slug}', [SiteController::class, 'post'])->name('posts.show');
Route::get('/pengumuman', [SiteController::class, 'announcements'])->name('announcements.index');
Route::get('/agenda', [SiteController::class, 'agendas'])->name('agendas.index');
Route::get('/galeri', [SiteController::class, 'gallery'])->name('gallery.index');
Route::get('/layanan', [SiteController::class, 'layanan'])->name('layanan');
Route::get('/kontak', [SiteController::class, 'contact'])->name('contact');
Route::get('/halaman/{slug}', [SiteController::class, 'page'])->name('pages.show');
