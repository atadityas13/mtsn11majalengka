<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PushSubscription extends Model
{
    protected $fillable = [
        'endpoint',
        'endpoint_hash',
        'public_key',
        'auth_token',
        'content_encoding',
        'user_agent',
    ];

    public static function hashEndpoint(string $endpoint): string
    {
        return hash('sha256', $endpoint);
    }

    public static function storeFromPayload(array $payload, ?string $userAgent = null): self
    {
        $endpoint = (string) ($payload['endpoint'] ?? '');
        $keys = $payload['keys'] ?? [];

        return static::query()->updateOrCreate(
            ['endpoint_hash' => static::hashEndpoint($endpoint)],
            [
                'endpoint' => $endpoint,
                'public_key' => (string) ($keys['p256dh'] ?? ''),
                'auth_token' => (string) ($keys['auth'] ?? ''),
            'content_encoding' => (string) ($payload['contentEncoding'] ?? 'aes128gcm'),
            'user_agent' => $userAgent ? Str::limit($userAgent, 250, '') : null,
            ]
        );
    }
}
