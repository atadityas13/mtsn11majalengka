<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_home_page_ok(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('MTsN 11 Majalengka');
    }

    public function test_berita_index_ok(): void
    {
        $this->get(route('posts.index'))->assertOk()->assertSee('Berita');
    }

    public function test_berita_detail_ok(): void
    {
        $this->get(route('posts.show', 'upacara-bendera-semarakkan-awal-pekan'))
            ->assertOk()
            ->assertSee('Upacara Bendera');
    }

    public function test_profil_page_ok(): void
    {
        $this->get(route('pages.show', 'profil'))->assertOk()->assertSee('Profil');
    }

    public function test_admin_login_ok(): void
    {
        $this->get('/admin/login')->assertOk();
    }
}
