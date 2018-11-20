<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class OrderControllerTest extends TestCase
{
    use WithoutMiddleware;

    public function testOrdersApiIntegration()
    {
        echo "\n \n Starts Executing API Integration Test \n \n";

        $createResponse = $this->json('POST', '/orders', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);
        $createResponse->assertStatus(200);
        echo "\n \n Creating order \n \n";

        $getResponse = $this->json('GET', '/orders?page=1&limit=10');
        $getResponse->assertStatus(200);
        echo "\n \n Fetching orders \n \n";

        $randId = rand(2,40);
        $updateResponse = $this->json('PATCH', '/orders/'.$randId, ["status" => "TAKEN"]);
        $updateResponse->assertStatus(200);
        echo "\n \n Updating order \n \n";
        
        echo "\n \n API Integration Test Execution Finished \n \n";
    }
}
