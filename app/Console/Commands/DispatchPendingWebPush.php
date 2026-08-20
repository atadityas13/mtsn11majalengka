<?php

namespace App\Console\Commands;

use App\Services\WebPushService;
use Illuminate\Console\Command;

class DispatchPendingWebPush extends Command
{
    protected $signature = 'webpush:dispatch-pending';

    protected $description = 'Kirim push untuk konten yang sudah tayang tapi belum dinotifikasi';

    public function handle(WebPushService $webPush): int
    {
        if (! $webPush->isConfigured()) {
            $this->warn('VAPID keys belum dikonfigurasi.');

            return self::SUCCESS;
        }

        $total = 0;

        foreach (WebPushService::notifiableModels() as $class) {
            $items = $class::query()
                ->whereNull('push_sent_at')
                ->orderBy('id')
                ->get();

            foreach ($items as $item) {
                $webPush->notifyModel($item);
                $total++;
                $this->line(class_basename($class).' #'.$item->getKey());
            }
        }

        $this->info('Selesai memproses '.$total.' item.');

        return self::SUCCESS;
    }
}
