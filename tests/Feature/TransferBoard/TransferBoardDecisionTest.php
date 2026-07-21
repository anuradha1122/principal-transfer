<?php

namespace Tests\Feature\TransferBoard;

use Tests\TestCase;

class TransferBoardDecisionTest extends TestCase
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
