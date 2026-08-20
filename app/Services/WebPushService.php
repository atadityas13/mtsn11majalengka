<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Post;
use App\Models\PushSubscription;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    public function isConfigured(): bool
    {
        return filled(config('services.webpush.public_key'))
            && filled(config('services.webpush.private_key'));
    }

    public function publicKey(): ?string
    {
        $key = config('services.webpush.public_key');

        return filled($key) ? (string) $key : null;
    }

    /**
     * @param  array{title: string, body?: string, url?: string, icon?: string}  $payload
     */
    public function sendToAll(array $payload): int
    {
        if (! $this->isConfigured()) {
            return 0;
        }

        $webPush = $this->client();
        $json = json_encode([
            'title' => $payload['title'],
            'body' => $payload['body'] ?? '',
            'url' => $payload['url'] ?? url('/'),
            'icon' => $payload['icon'] ?? $this->defaultIcon(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $sent = 0;

        PushSubscription::query()->orderBy('id')->chunkById(100, function ($subscriptions) use ($webPush, $json, &$sent): void {
            foreach ($subscriptions as $subscription) {
                try {
                    $webPush->queueNotification(
                        Subscription::create([
                            'endpoint' => $subscription->endpoint,
                            'publicKey' => $subscription->public_key,
                            'authToken' => $subscription->auth_token,
                            'contentEncoding' => $subscription->content_encoding ?: 'aes128gcm',
                        ]),
                        $json
                    );
                    $sent++;
                } catch (\Throwable $e) {
                    Log::warning('webpush.queue_failed', [
                        'subscription_id' => $subscription->id,
                        'message' => $e->getMessage(),
                    ]);
                }
            }

            foreach ($webPush->flush() as $report) {
                if ($report->isSuccess()) {
                    continue;
                }

                $endpoint = $report->getRequest()?->getUri()?->__toString();
                $code = $report->getResponse()?->getStatusCode();

                if (in_array($code, [404, 410], true) && $endpoint) {
                    PushSubscription::query()
                        ->where('endpoint_hash', PushSubscription::hashEndpoint($endpoint))
                        ->delete();
                }
            }
        });

        return $sent;
    }

    public function notifyPost(Post $post): void
    {
        if ($post->push_sent_at || ! $this->isPublicPost($post)) {
            return;
        }

        $post->forceFill(['push_sent_at' => now()])->saveQuietly();

        $site = SiteSetting::current();

        $this->sendToAll([
            'title' => 'Berita baru — '.$site->school_name,
            'body' => $post->title,
            'url' => route('posts.show', $post->slug),
            'icon' => $this->defaultIcon($site),
        ]);
    }

    public function notifyAnnouncement(Announcement $announcement): void
    {
        if ($announcement->push_sent_at || ! $announcement->is_published) {
            return;
        }

        $announcement->forceFill(['push_sent_at' => now()])->saveQuietly();

        $site = SiteSetting::current();

        $this->sendToAll([
            'title' => 'Pengumuman baru — '.$site->school_name,
            'body' => $announcement->title,
            'url' => route('announcements.index'),
            'icon' => $this->defaultIcon($site),
        ]);
    }

    public function isPublicPost(Post $post): bool
    {
        return $post->is_published
            && $post->published_at
            && $post->published_at->lte(now());
    }

    protected function client(): WebPush
    {
        return new WebPush([
            'VAPID' => [
                'subject' => config('services.webpush.subject', config('app.url')),
                'publicKey' => config('services.webpush.public_key'),
                'privateKey' => config('services.webpush.private_key'),
            ],
        ]);
    }

    protected function defaultIcon(?SiteSetting $site = null): string
    {
        $site ??= SiteSetting::current();

        if ($site->logo) {
            return asset('storage/'.$site->logo);
        }

        if ($site->favicon) {
            return asset('storage/'.$site->favicon);
        }

        return url('/favicon.ico');
    }
}
