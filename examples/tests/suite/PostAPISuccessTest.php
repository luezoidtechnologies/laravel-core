<?php

namespace Tests\Suite;

use \Tests\TestCase;

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

}
