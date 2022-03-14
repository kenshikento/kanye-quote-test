<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KanyeServiceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_api_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_randomizer_return_five_unique_results()
    {
        //
    }
}
