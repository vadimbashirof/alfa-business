<?php

declare(strict_types=1);

namespace App\Application\HttpClient\Exception;

use Psr\Http\Message\ResponseInterface;

interface HttpClientExceptionInterface extends \Throwable
{
    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface;
}
