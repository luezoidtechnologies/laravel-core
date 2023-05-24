<?php

namespace Tests\Suite;

use Luezoid\Models\Minion;
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
        $minion = $this->postJson('/api/minions', [
            'name' => 'Stuart',
            'totalEyes' => 2,
            'favouriteSound' => 'Grrrrrrrrrrr',
            'hasHairs' => true,
        ]);
        // Get the minion.
        $minion = Minion::where('id', 1)->first();
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
                'id' => $minion->id,
                'name' => $payload['name'],
                'totalEyes' => $payload['totalEyes'],
                'favouriteSound' => $payload['favouriteSound'],
                'hasHairs' => $payload['hasHairs'],
            ],
            'type' => null,
        ]);
    }
}
