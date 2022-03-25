<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\KanyeQuoteAPI;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KanyeServiceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_api_returns_a_successful_response()
    {
        //$response = new Kanye

        //$response->assertStatus(200);
    }

    /**
     * Test if the values that come from service are unique
     *
     * @return void
     */
    public function test_randomizer_return_five_unique_results() : void
    {
        $result = (new KanyeQuoteAPI())->randomizer(5);
        $this->assertTrue($result->duplicates()->isEmpty()); 
    }

    /**
     * Test if status is connection failure so check if output is a status even
     *
     * @return void
     */
    public function test_assert_connection_status_code_failure() : void
    {
        $result = (new KanyeQuoteAPI(' '))->getStatusCode(1);

        $this->assertTrue($result->reject(fn($status)=> is_int($status))->isNotEmpty()); 
    }
}
