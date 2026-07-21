<?php

namespace Tests\Feature\Provincial;

use Tests\TestCase;

class ProvincialTransferReviewTest extends TestCase
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
