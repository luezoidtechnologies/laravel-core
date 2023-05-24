<?php

namespace Tests\Suite;

use Luezoid\Models\Minion;
use Tests\TestCase;
require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Models/Minion.php';
require_once __DIR__ . '/../../Requests/MinionDeleteRequest.php';

class DeleteAPISuccessTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_delete_minion()
    {
        $payload = [
            'name' => 'Stuart',
            'totalEyes' => 2,
            'favouriteSound' => 'Grrrrrrrrrrr',
            'hasHairs' => true,
        ];
        // Create a minion.
        $minion = $this->postJson('/api/minions', $payload);
        $minion = Minion::where('id', 1)->first();
        // Make the request.
        $response = $this->deleteJson('/api/minions/1');
        // Assert that the response is successful.
        $response->assertOk();
        // Assert that the minion is deleted.
        $response->assertJson([
            'message' =>"Resource deleted successfully",
            'data' => [
                'id' => $minion->id,
                'name' => $minion->name,
                'totalEyes' => $minion->total_eyes,
                'favouriteSound' => $minion->favourite_sound,
                'hasHairs' => $minion->has_hairs
            ],
            'type' => null,
        ]);
    }
}
