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

    public function testOrders()
    {
        echo "\n \n Starts Executing Unit Test Cases \n \n";

        $response = $this->json('GET', '/orders?page=1&limit=10');

        $response->assertStatus(200);

        echo "\n \n GET Orders test case passed \n \n";
    }

    public function testOrdersRequestParamMisiing()
    {
        $response = $this->json('GET', '/orders');

        $response->assertStatus(406);

        echo "\n \n GET Orders without parameters test case passed \n \n";
    }

    public function testOrdersRequestParamTypeInvalid()
    {
        $response = $this->json('GET', '/orders?page=abc&limit=10');

        $response->assertStatus(406);

        echo "\n \n GET Orders with invalid parameter type test case passed \n \n";
    }

    public function testOrdersNoDataFound()
    {
        $response = $this->json('GET', '/orders?page=10001&limit=10');

        $response->assertStatus(204);

        echo "\n \n GET Orders no data found test case passed \n \n";
    }

    public function testStore()
    {
        $response = $this->json('POST', '/orders', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);

        $response->assertStatus(200);

        echo "\n \n Create Order test case passed \n \n";
    }

    public function testStoreOriginDestinationDuplicate()
    {
        $response = $this->json('POST', '/orders', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "28.704060",
                "77.102493"
            ]
        ]);

        $response->assertStatus(406);

        echo "\n \n Create Order duplicate origin and destination test case passed \n \n";
    }

    public function testStoreMissingOriginRequest()
    {
        $response = $this->json('POST', '/orders', [
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);

        $response->assertStatus(406);

        echo "\n \n Create Order missing origin test case passed \n \n";
    }

    public function testStoreMissingDestinationRequest()
    {
        $response = $this->json('POST', '/orders', [
            "origin" => [
                "28.704060",
                "77.102493"
            ]
        ]);

        $response->assertStatus(406);

        echo "\n \n Create Order missing destination test case passed \n \n";
    }

    public function testStoreStartLatitudeMissing()
    {
        $response = $this->json('POST', '/orders', [
            "origin" => [
                "",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);

        $response->assertStatus(406);

        echo "\n \n Create Order start lattitude missing test case passed \n \n";
    }

    public function testStoreStartLongitudeMissing()
    {
        $response = $this->json('POST', '/orders', [
            "origin" => [
                "28.704060",
                ""
            ],
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);

        $response->assertStatus(406);

        echo "\n \n Create Order start longitude missing test case passed \n \n";
    }

    public function testStoreEndLatitudeMissing()
    {
        $response = $this->json('POST', '/orders', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "",
                "77.391029"
            ]
        ]);

        $response->assertStatus(406);

        echo "\n \n Create Order end latitude missing test case passed \n \n";
    }

    public function testStoreEndLongitudeMissing()
    {
        $response = $this->json('POST', '/orders', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                ""
            ]
        ]);

        $response->assertStatus(406);

        echo "\n \n Create Order end longitude missing test case passed \n \n";
    }

    public function testStoreInvalidLatitudeRange()
    {
        $response = $this->json('POST', '/orders', [
            "origin" => [
                "98.704060",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);

        $response->assertStatus(406);

        echo "\n \n Create Order Invalid latitude range test case passed \n \n";
    }

    public function testStoreInvalidLonitudeRange()
    {
        $response = $this->json('POST', '/orders', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                "197.391029"
            ]
        ]);

        $response->assertStatus(406);

        echo "\n \n Create Order Invalid longitude range test case passed \n \n";
    }

    public function testUpdate()
    {
        $randOrderId = rand(2,40);
        $response = $this->json('PATCH', '/orders/'.$randOrderId, [
                "status" => "TAKEN"
        ]);

        $response->assertStatus(200);

        echo "\n \n Update Order test case passed \n \n";
    }

    public function testUpdateMissingRequestBody()
    {
        $response = $this->json('PATCH', '/orders/1', []);

        $response->assertStatus(406);

        echo "\n \n Update Order missing parameter test case passed \n \n";
    }

    public function testUpdateInvalidId()
    {
        $response = $this->json('PATCH', '/orders/101a', []);

        $response->assertStatus(406);

        echo "\n \n Update Order invalid order id test case passed \n \n";
    }

    public function testUpdateOrderAlreadyTaken()
    {
        $response = $this->json('PATCH', '/orders/1', [
                "status" => "TAKEN"
        ]);

        $response->assertStatus(409);

        echo "\n \n Update Order order already taken test case passed \n \n";
        
        echo "\n \n Unit Test Cases Execution Finished \n \n";
    }
}
