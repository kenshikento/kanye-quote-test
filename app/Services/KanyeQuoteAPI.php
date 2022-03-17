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

    public Collection $response;
    
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

    private function sendRequest() : self
    {
        $total = $this->reqQuotes;
        $client = $this->client;
        $endpoint = $this->url;

        $requests = function ($total) use ($endpoint){
            for ($i = 0; $i < $total; $i++) {
                yield new Request('GET', $endpoint);
            }
        };

        $response =  Pool::batch($client, $requests($total), ['concurrency' => 5]);
        
        $this->response = $this->processMultiResponse($response);

        return $this;
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

    public function randomizer(int $total) 
    {
        $this->reqQuotes = $total; 
        $this->sendRequest($this->url);
        
        return $this->response;    
    }
}