<?php

namespace App\Services\AlfaBusiness\Request;

use App\Application\HttpClient\HttpClientInterface;
use App\Services\AlfaBusiness\AlfaBusinessIDAuthService;

class TokenRequest extends Request
{
    private const ENDPOINT = '/oidc/token';
    private const GRANT_TYPE = 'authorization_code';
    private const METHOD = HttpClientInterface::METHOD_POST;

    public function __construct(string $code)
    {
        parent::__construct(
            endpoint: self::ENDPOINT,
            accept: 'application/json',
            contentType: 'application/x-www-form-urlencoded',
            method: self::METHOD,
            params: [
                'grant_type' => self::GRANT_TYPE,
                'code' => $code,
            ],
            isBearer: false,
            isClientId: true,
            isClientSecret: true,
            isRedirectUri: true,
            redirectEndpoint: AlfaBusinessIDAuthService::ENDPOINT_REDIRECT_SET_TOKEN,
        );
    }
}
