<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class StoreControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreCanBeCreated(): void
    {
        $data = [
            'name' => 'Test',
            "lat" => "-1.4237857249",
            "long" => "0",
            "state" => "open",
            "type" => "takeaway",
            "max_delivery_distance" => "300"
        ];
        $response = $this->postJson('/store', $data);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('stores', $data);
    }

    public function testStoreCannotBeCreatedWithErroneousData(): void
    {
        $data = [
            'name' => 'Test',
            "lat" => "-1.4237857249",
        ];
        $response = $this->postJson('/store', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing('stores', $data);
    }
}
