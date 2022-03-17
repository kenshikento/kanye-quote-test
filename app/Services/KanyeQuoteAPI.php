<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

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

    private function sendRequest($endpoint, $batch = false)
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

    private function sendMultiRequest($endpoint) : Collection
    {
        $total = $this->reqQuotes;
        $client = new Client();

        $requests = function ($total) use ($endpoint){
            for ($i = 0; $i < $total; $i++) {
                yield new Request('GET', $endpoint);
            }
        };

        $response = Pool::batch($client,$requests($total),['concurrency' => 5]);
        return $this->processMultiResponse($response);
    }

    /**
     * Just processes the Response by json decoding then iterating through as a collection 
     *
     * @param array $items
     * @return Collection
     */
    private function processMultiResponse(array $items) : Collection
    {
        return collect($items)->map(fn($q)=> json_decode($q->getBody())->quote);
    }

    public function randomizer(/*Int $quote*/) 
    {
        $this->reqQuotes = 5; 

        $result = $this->sendRequest($this->url, true);
        dd($result);
    }
}