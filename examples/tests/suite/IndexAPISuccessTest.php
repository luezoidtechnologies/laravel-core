<?php

namespace Tests\Suite;
use Luezoid\Models\Minion;
use Tests\TestCase;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Models/Minion.php';

class IndexAPISuccessTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index_minion(): void
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
        $response = $this->getJson('/api/minions');

        $response->assertOk();

        // Assert that the response data is correct.
        $response->assertJson([
            'message' => null,
            'data' => [
                'items' => [
                    [
                        'id' => $minion->id,
                        'name' => $minion->name,
                        'totalEyes' => $minion->total_eyes,
                        'favouriteSound' => $minion->favourite_sound,
                        'hasHairs' => $minion->has_hairs
                    ]
                ],
                'page' => 1,
                'total' => 1,
                'pages' => 1,
                'perpage' => 15,
            ],
            'type' => null,
        ]);
    }
}
