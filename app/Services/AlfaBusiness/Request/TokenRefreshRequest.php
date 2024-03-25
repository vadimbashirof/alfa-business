<?php

namespace App\Services\AlfaBusiness\Request;

use App\Application\HttpClient\HttpClientInterface;

class TokenRefreshRequest extends Request
{
    private const ENDPOINT = '/oidc/token';
    private const GRANT_TYPE = 'refresh_token';
    private const METHOD = HttpClientInterface::METHOD_POST;

    public function __construct(string $refreshToken)
    {
        parent::__construct(
            endpoint: self::ENDPOINT,
            accept: 'application/json',
            contentType: 'application/x-www-form-urlencoded',
            method: self::METHOD,
            params: [
                'grant_type' => self::GRANT_TYPE,
                'refresh_token' => $refreshToken,
            ],
            isBearer: false,
            isClientId: true,
            isClientSecret: true,
        );
    }
}
