<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test halaman utama aplikasi.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->followingRedirects()->get('/login');
        $response->assertStatus(200);
    }
}
