<?php

declare(strict_types=1);

namespace App\Application\HttpClient;

use App\Application\HttpClient\Guzzle\HttpClient;
use App\Application\HttpClient\Guzzle\RequestOptionsTranslator;
use GuzzleHttp\Client;

class HttpClientFactory
{
    /**
     * @return HttpClient
     */
    public static function create(): HttpClient
    {
        return new HttpClient(
            new Client(),
            new RequestOptionsTranslator(),
        );
    }
}
