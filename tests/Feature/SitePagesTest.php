<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
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

    public function test_berita_search_ok(): void
    {
        $this->get(route('posts.index', ['q' => 'Upacara']))
            ->assertOk()
            ->assertSee('Upacara Bendera');
    }

    public function test_berita_detail_ok(): void
    {
        $this->get(route('posts.show', 'upacara-bendera-semarakkan-awal-pekan'))
            ->assertOk()
            ->assertSee('Upacara Bendera')
            ->assertSee('og:title', false);
    }

    public function test_profil_page_ok(): void
    {
        $this->get(route('pages.show', 'profil'))->assertOk()->assertSee('Profil');
    }

    public function test_agenda_calendar_ok(): void
    {
        $this->get(route('agendas.index'))->assertOk()->assertSee('Agenda');
    }

    public function test_prestasi_ok(): void
    {
        $this->get(route('achievements.index'))->assertOk()->assertSee('Prestasi');
    }

    public function test_struktur_organisasi_ok(): void
    {
        $this->get(route('organization.index'))
            ->assertOk()
            ->assertSee('Struktur Organisasi')
            ->assertSee('Kepala Madrasah');
    }

    public function test_staff_ok(): void
    {
        $this->get(route('staff.index'))->assertOk()->assertSee('Guru');
    }

    public function test_downloads_ok(): void
    {
        $this->get(route('downloads.index'))->assertOk()->assertSee('Unduhan');
    }

    public function test_contact_form_stores_message(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Orang Tua',
            'email' => 'ortu@example.com',
            'phone' => '08123456789',
            'subject' => 'Info PPDB',
            'message' => 'Assalamu\'alaikum, ingin bertanya jadwal PPDB.',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'ortu@example.com',
            'subject' => 'Info PPDB',
        ]);

        $this->assertSame(1, ContactMessage::query()->count());
    }

    public function test_manifest_ok(): void
    {
        $this->get(route('manifest'))->assertOk();
    }

    public function test_admin_login_ok(): void
    {
        $this->get('/admin/login')->assertOk();
    }
}
