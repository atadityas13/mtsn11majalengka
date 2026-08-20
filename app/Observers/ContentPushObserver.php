<?php

namespace App\Observers;

use App\Models\Announcement;
use App\Models\Post;
use App\Services\WebPushService;
use Illuminate\Support\Facades\Log;

class ContentPushObserver
{
    public function __construct(protected WebPushService $webPush)
    {
    }

    public function saved(Post|Announcement $model): void
    {
        if (! $this->webPush->isConfigured()) {
            return;
        }

        try {
            dispatch(function () use ($model): void {
                $fresh = $model->newQuery()->find($model->getKey());

                if (! $fresh) {
                    return;
                }

                if ($fresh instanceof Post) {
                    app(WebPushService::class)->notifyPost($fresh);
                }

                if ($fresh instanceof Announcement) {
                    app(WebPushService::class)->notifyAnnouncement($fresh);
                }
            })->afterResponse();
        } catch (\Throwable $e) {
            Log::warning('webpush.dispatch_failed', ['message' => $e->getMessage()]);
        }
    }
}
