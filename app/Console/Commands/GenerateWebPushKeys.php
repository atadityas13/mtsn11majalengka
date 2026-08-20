<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateWebPushKeys extends Command
{
    protected $signature = 'webpush:vapid';

    protected $description = 'Generate VAPID keys for browser push notifications';

    public function handle(): int
    {
        try {
            $keys = VAPID::createVapidKeys();
        } catch (\Throwable $e) {
            $this->error('Gagal generate lewat OpenSSL PHP: '.$e->getMessage());
            $this->newLine();
            $this->warn('Jalankan alternatif:');
            $this->line('  npx web-push generate-vapid-keys');
            $this->newLine();

            return self::FAILURE;
        }

        $this->info('Tambahkan ke file .env:');
        $this->newLine();
        $this->line('VAPID_PUBLIC_KEY='.$keys['publicKey']);
        $this->line('VAPID_PRIVATE_KEY='.$keys['privateKey']);
        $this->line('VAPID_SUBJECT='.config('app.url'));
        $this->newLine();

        return self::SUCCESS;
    }
}
