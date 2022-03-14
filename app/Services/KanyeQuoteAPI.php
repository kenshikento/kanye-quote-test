<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\GuzzleException;

class KanyeQuoteAPI
{
    protected ?string $url;

    protected Client $client;

    public int $reqQuotes;

    public function __construct() 
    {
        $this->url = config('services.kanye.url');
        $this->client = app()->get(Client::class);
        $this->validateSetup();
    }

    /**
     * Validates any missing config 
     *
     * @return void
     */
    private function validateSetup(): void
    {
        if(!$this->url) {
            throw new \Exception('Please set the URL .env');
        }
    }

    private function sendRequest($endpoint, $batch = false) : Response
    {
        return match($batch){
            true => $this->sendMultiRequest($endpoint),
            false => $this->sendSingleRequest($endpoint)
        };
    }
    
    private function sendSingleRequest($endpoint) : Response 
    {
        return $this->client->get($endpoint);
    }

    private function sendMultiRequest($endpoint) : Response
    {
        $requests = function($this->reqQuotes) use($endpoint) {
            for ($i = 0; $i < $this->reqQuotes; $i++) {
                yield new Request('GET', $uri);
            }
        };
    }

    public function randomizer(/*Int $quote*/): Collection 
    {
        $this->reqQuotes = 5; 

        $result = $this->sendRequest($this->url, true);
        if($result->getStatusCode() ) {
            
        }   
        dd($result->isSuccesful());
        dd(json_decode($result->getBody()));
    }
}