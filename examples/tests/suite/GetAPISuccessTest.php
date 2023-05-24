<?php

namespace Tests\Suite;

use Tests\TestCase;
require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Models/Minion.php';

class GetAPISuccessTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_minion()
    {
        $payload = [
            'name' => 'Stuart',
            'totalEyes' => 2,
            'favouriteSound' => 'Grrrrrrrrrrr',
            'hasHairs' => true,
        ];
        // Create a minion.
        $this->postJson('/api/minions', $payload);

        // Make the request.
        $response = $this->getJson('/api/minions/1');

        // Assert that the response is successful.
        $response->assertOk();

        // Assert that the response data is correct.
        $response->assertJson([
            'message' => null,
            'data' => [
                'id' => 1,
                'name' => 'Stuart',
                'totalEyes' => 2,
                'favouriteSound' => 'Grrrrrrrrrrr',
                'hasHairs' => true
            ],
            'type' => null,
        ]);
    }
}
