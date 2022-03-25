<?php

namespace App\Services;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Log;

class KanyeQuoteAPI
{
    protected ?string $url;

    protected Client $client;

    public int $reqQuotes;

    public Collection $response;

    private Collection $statusCodes;

    private Collection $error;
    
    public function __construct($url = null) 
    {
        $this->url = config('services.kanye.url');
        
        if($url) {
            $this->url = $url;
        }
        
        $this->client = app()->get(Client::class);
        $this->validateSetup();
        
        return $this;
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

    /**
     * Method that sends request as Pool and so sets the conditions.
     *
     * @return self
     */
    public function sendRequest() : self
    {
        $client = $this->client;
        $total = $this->reqQuotes;
        $endpoint = $this->url;

        $requests = function ($total) use ($endpoint){
            for ($i = 0; $i < $total; $i++) {
                yield new Request('GET', $endpoint);
            }
        };

        $response =  Pool::batch($client, $requests($total), ['concurrency' => 5]);

        $this->status = $this->setStatusResponse($response);

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
        return collect($items)
            ->reject(fn($q)=> $q instanceof ConnectException OR $q instanceof RequestException)
            ->map(fn(Response $q)=> json_decode($q->getBody())->quote)
        ;
    }

    /**
     * Sets all status codes into status and add any exceptions into error logs
     *
     * @param array $items
     * @return Collection
     */
    private function setStatusResponse(array $items) : Collection
    {
        return collect($items)->map(function($q){
            if($q instanceof ConnectException){
                Log::info('Error Occurred with connection');
                return $q->getMessage();
            }

            if($q instanceof RequestException){
                return $q->getStatusCode();
            }

            return $q->getStatusCode();
        });
    }

    /**
     * Get Status Code
     * Runs the request which also sets request
     * @param int $total
     * @return Collection
     */
    public function getStatusCode(int $total) : Collection 
    {
        $this->randomizer($total);
        return $this->status; 
    }

    // technically it randomize itself from Api call
    public function randomizer(int $total) 
    {
        $this->reqQuotes = $total; 
        $this->sendRequest($this->url);

        return $this->response;    
    }
}