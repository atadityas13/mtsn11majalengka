<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_push_config_reports_disabled_without_keys(): void
    {
        config([
            'services.webpush.public_key' => null,
            'services.webpush.private_key' => null,
        ]);

        $this->getJson(route('push.config'))
            ->assertOk()
            ->assertJson([
                'enabled' => false,
                'publicKey' => null,
            ]);
    }

    public function test_push_config_reports_enabled_with_keys(): void
    {
        config([
            'services.webpush.public_key' => 'test-public',
            'services.webpush.private_key' => 'test-private',
        ]);

        $this->getJson(route('push.config'))
            ->assertOk()
            ->assertJson([
                'enabled' => true,
                'publicKey' => 'test-public',
            ]);
    }

    public function test_can_store_push_subscription(): void
    {
        $this->postJson(route('push.subscribe'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
            'keys' => [
                'p256dh' => 'BPtestpublickey',
                'auth' => 'testauth',
            ],
        ])->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseCount('push_subscriptions', 1);
    }
}
