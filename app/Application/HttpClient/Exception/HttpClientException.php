<?php

declare(strict_types=1);

namespace App\Application\HttpClient\Exception;

use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class HttpClientException extends \RuntimeException implements HttpClientExceptionInterface
{
    private ?ResponseInterface $response;

    /**
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param ResponseInterface|null $response
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        \Throwable $previous = null,
        ResponseInterface $response = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }

    /**
     * @inheritDoc
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
