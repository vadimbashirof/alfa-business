<?php

declare(strict_types=1);

namespace App\Application\HttpClient\Guzzle;

use App\Application\HttpClient\Exception\HttpClientException;
use App\Application\HttpClient\HttpClientInterface;
use App\Application\HttpClient\RequestOption\AbstractRequestOption;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class HttpClient implements HttpClientInterface
{
    private Client $client;
    private RequestOptionsTranslator $optionsTranslator;

    /**
     * @param Client $client
     * @param RequestOptionsTranslator $optionsTranslator
     */
    public function __construct(Client $client, RequestOptionsTranslator $optionsTranslator)
    {
        $this->client = $client;
        $this->optionsTranslator = $optionsTranslator;
    }

    /**
     * @inheritDoc
     */
    public function request(string $method, string $url, AbstractRequestOption ...$options): ResponseInterface
    {
        try {
            $translatedOptions = $this->optionsTranslator->translate(...$options);

            return $this->client->request($method, $url, $this->setUserAgentIfNotExists($translatedOptions));
        } catch (GuzzleException $e) {
            $response = $e instanceof RequestException ? $e->getResponse() : null;

            throw new HttpClientException($e->getMessage(), $e->getCode(), $e, $response);
        }
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function setUserAgentIfNotExists(array $options): array
    {
        if (!isset($options[RequestOptions::HEADERS]['User-Agent'])) {
            $userAgent = trim(getenv('APP_NAME') . '_' . getenv('APP_VERSION'), '_');

            if ($userAgent) {
                $options[RequestOptions::HEADERS]['User-Agent'] = $userAgent;
            }
        }

        return $options;
    }
}
