<?php

declare(strict_types=1);

namespace App\Application\HttpClient;

use App\Application\HttpClient\Exception\HttpClientExceptionInterface;
use App\Application\HttpClient\RequestOption\AbstractRequestOption;
use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    public const
        METHOD_GET = 'GET',
        METHOD_PUT = 'PUT',
        METHOD_POST = 'POST',
        METHOD_DELETE = 'DELETE';

    /**
     * @param string $method
     * @param string $url
     * @param AbstractRequestOption ...$options
     *
     * @return ResponseInterface
     * @throws HttpClientExceptionInterface
     */
    public function request(string $method, string $url, AbstractRequestOption ...$options): ResponseInterface;
}
