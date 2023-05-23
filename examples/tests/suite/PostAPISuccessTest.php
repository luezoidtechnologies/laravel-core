<?php

namespace Tests\Suite;

use Tests\TestCase;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Models/Minion.php';

class PostAPISuccessTest extends TestCase
{
    public function test_post_api()
    {
        $payload = [
            'name' => 'Stuart',
            'totalEyes' => 2,
            'favouriteSound' => 'Grrrrrrrrrrr',
            'hasHairs' => true,
        ];

        $response = $this->postJson('/api/minions', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Resource Created successfully',
            'data' => [
                'name' => 'Stuart',
                'totalEyes' => 2,
                'favouriteSound' => 'Grrrrrrrrrrr',
                'hasHairs' => true,
                'id' => 1,
            ],
            'type' => null,
        ]);
    }

    /*public function test_list_api()
    {
        $response = $this->get('/api/minions');
        $response->assertStatus(200);
        $response->assertJson([
            "message" => null,
            "data" => [
                "items" => [
                    [
                        "id" => 1,
                        "name" => "Lucifer",
                        "totalEyes" => 0,
                        "favouriteSound" => "Luuuuuuu",
                        "hasHairs" => true,
                        "missions" => [],
                        "leadingMission" => null
                    ]
                ],
                "page" => 1,
                "total" => 1,
                "pages" => 1,
                "perpage" => 15
            ],
            "type" => null
        ]);
    }*/

    /*public function test_show_api()
    {
        $response = $this->get('/api/minions/1');
        $response->assertStatus(200);
        $response->assertJson([
            "message" => null,
            "data" => [
                "id" => 1,
                "name" => "Lucifer",
                "totalEyes" => 0,
                "favouriteSound" => "Luuuuuuu",
                "hasHairs" => true,
                "missions" => [],
                "leadingMission" => null
            ],
            "type" => null
        ]);
    }*/
}
