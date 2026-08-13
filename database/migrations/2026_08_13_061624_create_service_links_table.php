<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_links', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->string('description')->nullable();
            $table->string('logo')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('open_in_new_tab')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        $settings = Schema::hasTable('site_settings')
            ? DB::table('site_settings')->first()
            : null;
        $now = now();

        DB::table('service_links')->insert([
            [
                'label' => 'PPDB Online',
                'url' => $settings->ppdb_url ?? 'https://ppdb.mtsn11majalengka.sch.id/',
                'description' => 'Pendaftaran peserta didik baru',
                'logo' => null,
                'sort_order' => 1,
                'open_in_new_tab' => true,
                'is_visible' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'label' => 'Rapor Digital',
                'url' => $settings->rdm_url ?? 'https://rdm.mtsn11majalengka.sch.id/',
                'description' => 'Portal RDM madrasah',
                'logo' => null,
                'sort_order' => 2,
                'open_in_new_tab' => true,
                'is_visible' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'label' => 'Kemenag RI',
                'url' => $settings->kemenag_url ?? 'https://kemenag.go.id/',
                'description' => 'Portal Kementerian Agama',
                'logo' => null,
                'sort_order' => 3,
                'open_in_new_tab' => true,
                'is_visible' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'label' => 'Kontak',
                'url' => '/kontak',
                'description' => 'Alamat & saluran komunikasi',
                'logo' => null,
                'sort_order' => 4,
                'open_in_new_tab' => false,
                'is_visible' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('service_links');
    }
};
