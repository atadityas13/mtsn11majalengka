<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\Download;
use App\Models\GalleryItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\PushSubscription;
use App\Models\SiteSetting;
use App\Models\StaffMember;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
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

    public function subscriberCount(): int
    {
        return PushSubscription::query()->count();
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
            'image' => $payload['image'] ?? null,
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

    public function notifyModel(Model $model): void
    {
        $meta = $this->metaFor($model);

        if (! $meta || $model->getAttribute('push_sent_at') || ! $meta['is_public']($model)) {
            return;
        }

        $model->forceFill(['push_sent_at' => now()])->saveQuietly();

        $site = SiteSetting::current();
        $icon = $meta['icon']($model) ?: $this->defaultIcon($site);

        $this->sendToAll([
            'title' => $meta['headline'].' — '.$site->school_name,
            'body' => $meta['body']($model),
            'url' => $meta['url']($model),
            'icon' => $icon,
            'image' => $meta['image']($model),
        ]);
    }

    /**
     * @return list<class-string<Model>>
     */
    public static function notifiableModels(): array
    {
        return [
            Post::class,
            Announcement::class,
            Agenda::class,
            GalleryItem::class,
            Achievement::class,
            Download::class,
            Video::class,
            Page::class,
            StaffMember::class,
        ];
    }

    /**
     * @return array{
     *     headline: string,
     *     body: callable(Model): string,
     *     url: callable(Model): string,
     *     is_public: callable(Model): bool,
     *     icon: callable(Model): ?string,
     *     image: callable(Model): ?string
     * }|null
     */
    protected function metaFor(Model $model): ?array
    {
        $storage = fn (?string $path): ?string => filled($path) ? asset('storage/'.$path) : null;

        return match (true) {
            $model instanceof Post => [
                'headline' => 'Berita baru',
                'body' => fn (Post $post): string => (string) $post->title,
                'url' => fn (Post $post): string => route('posts.show', $post->slug),
                'is_public' => fn (Post $post): bool => $post->is_published
                    && $post->published_at
                    && $post->published_at->lte(now()),
                'icon' => fn (Post $post): ?string => $storage($post->cover_image),
                'image' => fn (Post $post): ?string => $storage($post->cover_image),
            ],
            $model instanceof Announcement => [
                'headline' => 'Pengumuman baru',
                'body' => fn (Announcement $item): string => (string) $item->title,
                'url' => fn (): string => route('announcements.index'),
                'is_public' => fn (Announcement $item): bool => (bool) $item->is_published,
                'icon' => fn (): ?string => null,
                'image' => fn (): ?string => null,
            ],
            $model instanceof Agenda => [
                'headline' => 'Agenda baru',
                'body' => fn (Agenda $item): string => (string) $item->title,
                'url' => fn (): string => route('agendas.index'),
                'is_public' => fn (Agenda $item): bool => (bool) $item->is_published,
                'icon' => fn (): ?string => null,
                'image' => fn (): ?string => null,
            ],
            $model instanceof GalleryItem => [
                'headline' => 'Galeri baru',
                'body' => fn (GalleryItem $item): string => (string) ($item->title ?: 'Foto baru di galeri'),
                'url' => fn (): string => route('gallery.index'),
                'is_public' => fn (GalleryItem $item): bool => (bool) $item->is_published,
                'icon' => fn (GalleryItem $item): ?string => $storage($item->image),
                'image' => fn (GalleryItem $item): ?string => $storage($item->image),
            ],
            $model instanceof Achievement => [
                'headline' => 'Prestasi baru',
                'body' => fn (Achievement $item): string => (string) $item->title,
                'url' => fn (): string => route('achievements.index'),
                'is_public' => fn (Achievement $item): bool => (bool) $item->is_published,
                'icon' => fn (Achievement $item): ?string => $storage($item->image),
                'image' => fn (Achievement $item): ?string => $storage($item->image),
            ],
            $model instanceof Download => [
                'headline' => 'Unduhan baru',
                'body' => fn (Download $item): string => (string) $item->title,
                'url' => fn (): string => route('downloads.index'),
                'is_public' => fn (Download $item): bool => (bool) $item->is_published,
                'icon' => fn (): ?string => null,
                'image' => fn (): ?string => null,
            ],
            $model instanceof Video => [
                'headline' => $model->type === 'short' ? 'Short baru' : 'Video baru',
                'body' => fn (Video $item): string => (string) $item->title,
                'url' => fn (Video $item): string => $item->type === 'short'
                    ? route('shorts.index')
                    : route('videos.index'),
                'is_public' => fn (Video $item): bool => (bool) $item->is_published
                    && (! $item->published_at || $item->published_at->lte(now())),
                'icon' => fn (Video $item): ?string => $item->thumbnailUrl(),
                'image' => fn (Video $item): ?string => $item->thumbnailUrl(),
            ],
            $model instanceof Page => [
                'headline' => 'Halaman baru',
                'body' => fn (Page $item): string => (string) $item->title,
                'url' => fn (Page $item): string => route('pages.show', $item->slug),
                'is_public' => fn (Page $item): bool => (bool) $item->is_published,
                'icon' => fn (Page $item): ?string => $storage($item->hero_image),
                'image' => fn (Page $item): ?string => $storage($item->hero_image),
            ],
            $model instanceof StaffMember => [
                'headline' => 'Tenaga pendidik baru',
                'body' => fn (StaffMember $item): string => trim($item->name.($item->role ? ' — '.$item->role : '')),
                'url' => fn (): string => route('staff.index'),
                'is_public' => fn (StaffMember $item): bool => (bool) $item->is_published,
                'icon' => fn (StaffMember $item): ?string => $storage($item->photo),
                'image' => fn (StaffMember $item): ?string => $storage($item->photo),
            ],
            default => null,
        };
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
