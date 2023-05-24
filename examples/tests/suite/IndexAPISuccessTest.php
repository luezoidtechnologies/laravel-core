<?php

namespace Tests\Suite;
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
        $this->postJson('/api/minions', $payload);

        // Make the request.
        $response = $this->getJson('/api/minions');

        $response->assertOk();

        // Assert that the response data is correct.
        $response->assertJson([
            'message' => null,
            'data' => [
                'items' => [
                    [
                        'id' => 1,
                        'name' => 'Stuart',
                        'totalEyes' => 2,
                        'favouriteSound' => 'Grrrrrrrrrrr',
                        'hasHairs' => true
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
