<?php

namespace App\Observers;

use App\Services\WebPushService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ContentPushObserver
{
    public function __construct(protected WebPushService $webPush)
    {
    }

    public function saved(Model $model): void
    {
        if (! $this->webPush->isConfigured()) {
            return;
        }

        if (! in_array($model::class, WebPushService::notifiableModels(), true)) {
            return;
        }

        try {
            dispatch(function () use ($model): void {
                $fresh = $model->newQuery()->find($model->getKey());

                if (! $fresh) {
                    return;
                }

                app(WebPushService::class)->notifyModel($fresh);
            })->afterResponse();
        } catch (\Throwable $e) {
            Log::warning('webpush.dispatch_failed', ['message' => $e->getMessage()]);
        }
    }
}
