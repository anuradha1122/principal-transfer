<?php

namespace Tests\Feature\Principal;

use Tests\TestCase;

class PrincipalSelfProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
