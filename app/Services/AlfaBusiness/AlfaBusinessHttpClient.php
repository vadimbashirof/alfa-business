<?php

namespace App\Services\AlfaBusiness;

use App\Application\HttpClient\Exception\HttpClientExceptionInterface;
use App\Application\HttpClient\HttpClientInterface;
use App\Application\HttpClient\RequestOption\RequestOptionCertificate;
use App\Application\HttpClient\RequestOption\RequestOptionFormParams;
use App\Application\HttpClient\RequestOption\RequestOptionHeaders;
use App\Application\HttpClient\RequestOption\RequestOptionJSON;
use App\Application\HttpClient\RequestOption\RequestOptionQuery;
use App\Application\HttpClient\RequestOption\RequestOptionSSLKey;
use App\Application\HttpClient\RequestOption\RequestOptionSSLVerification;
use App\Infrastructure\Logger\AlfaBusinessLogger;
use App\Services\AlfaBusiness\Exception\AlfaBusinessHttpClientException;
use App\Services\AlfaBusiness\Request\Request;

class AlfaBusinessHttpClient
{
    public const TEST_ENV = 'test';
    public const PROD_ENV = 'prod';
    private array $config;

    public function __construct(
        array $config,
        private HttpClientInterface $httpClient,
        private AlfaBusinessLogger $logger
    ) {
        $this->config = $config;
    }

    /**
     * @throws AlfaBusinessHttpClientException
     */
    public function sendRequest(Request $request): array
    {
        $endpoint = $request->getEndpoint();
        $url = $this->config['base_url'] . $endpoint;
        $method = $request->getMethod();
        $params = $request->getParams();
        $headers = [
            'Accept' => $request->getAccept(),
            'Content-Type' => $request->getContentType(),
        ];
        $bearer = $request->getBearer();
        if ($request->isBearer() && $bearer) {
            $headers['Authorization'] = 'Bearer ' . $bearer;
        }

        $options = [
            new RequestOptionHeaders($headers),
            new RequestOptionCertificate($this->config['cert_file']),
            new RequestOptionSSLKey($this->config['key_file']),
            new RequestOptionSSLVerification(false),
        ];

        if ($request->isClientId()) {
            $params['client_id'] = $this->config['client_id'];
        }
        if ($request->isClientSecret()) {
            $params['client_secret'] = $this->config['client_secret'];
        }
        if ($request->isRedirectUri()) {
            $params['redirect_uri'] = $this->config['redirect_uri'] . $request->getRedirectEndpoint();
        }

        if ($method === HttpClientInterface::METHOD_POST && $params) {
            if ($request->getContentType() === 'application/x-www-form-urlencoded') {
                $options[] = new RequestOptionFormParams($params);
            } elseif ($request->getContentType() === 'application/json') {
                $options[] = new RequestOptionJSON($params);
            }
        }
        if ($method === HttpClientInterface::METHOD_GET && $params) {
            $options[] = new RequestOptionQuery($params);
        }

        try {
            $paramsForLogs = $params;
            if ($request->isClientId()) {
                $paramsForLogs['client_id'] = '***';
            }
            if ($request->isClientSecret()) {
                $paramsForLogs['client_secret'] = '***';
            }
            $this->logger->notice("Alfa business request params [endpoint {$endpoint}]:", [
                'url' => $url,
                'method' => $request->getMethod(),
                'params' => $paramsForLogs,
            ]);
            $response = $this->httpClient->request(
                $request->getMethod(),
                $url,
                ...$options
            );
        } catch (HttpClientExceptionInterface $e) {
            $body = $e->getResponse();
            $message = $e->getMessage();

            $this->logger->critical(
                "Alfa business request failed [endpoint {$endpoint}]: {$message}}",
                [
                    'url' => $url,
                    'exception' => $e,
                    'body' => $body,
                ]
            );

            throw new AlfaBusinessHttpClientException(
                "Alfa business error has occurred while sending request [$url] to service. $message"
            );
        }

        $body = $response->getBody()->getContents();
        $result = json_decode($body, true);
        $result = is_array($result) ? $result : [];
        $this->logger->notice("Alfa business response [endpoint {$endpoint}]:", [
            'url' => $url,
            'body' => $result,
        ]);

        return $result;
    }
}
