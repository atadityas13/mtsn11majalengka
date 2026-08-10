<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_health_endpoint_ok(): void
    {
        $this->get('/up')->assertOk();
    }
}
