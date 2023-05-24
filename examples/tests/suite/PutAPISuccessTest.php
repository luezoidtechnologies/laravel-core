<?php

namespace Tests\Suite;

use Tests\TestCase;
require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Models/Minion.php';
require_once __DIR__ . '/../../Requests/MinionUpdateRequest.php';

class PutAPISuccessTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_update_minion()
    {
        // Create a minion.
        $this->postJson('/api/minions', [
            'name' => 'Stuart',
            'totalEyes' => 2,
            'favouriteSound' => 'Grrrrrrrrrrr',
            'hasHairs' => true,
        ]);

        // Prepare the request payload.
        $payload = [
            'name' => 'Stuart',
            'totalEyes' => 2,
            'favouriteSound' => 'Hrrrrrrrrrrr',
            'hasHairs' => true,
        ];

        // Make the request.
        $response = $this->putJson('/api/minions/1', $payload);

        // Assert that the response is successful.
        $response->assertOk();

        // Assert that the response data is correct.
        $response->assertJson([
            'message' => 'Resource Updated successfully',
            'data' => [
                'id' => 1,
                'name' => 'Stuart',
                'totalEyes' => 2,
                'favouriteSound' => 'Hrrrrrrrrrrr',
                'hasHairs' => true
            ],
            'type' => null,
        ]);
    }
}
