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
    public function test_randomizer_return_five_unique_results()
    {
        $result = (new KanyeQuoteAPI())->randomizer(5);
        $this->assertTrue($result->duplicates()->isEmpty()); 
    }

    public function test_assert_status_code_failure()
    {
        $result = (new KanyeQuoteAPI('null'))->getStatusCode(1);
        
        dd($result);

        $result[0] === 200 ? $this->assertTrue(true) : $this->assertTrue(false); 
    }
}
