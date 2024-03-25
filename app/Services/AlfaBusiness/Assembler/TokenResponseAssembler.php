<?php

namespace App\Services\AlfaBusiness\Assembler;

use App\Application\Logger\AlfaBusinessLogger;
use App\Services\AlfaBusiness\Exception\AlfaBusinessAccessTokenErrorException;
use App\Services\AlfaBusiness\Response\TokenResponse;
use DateInterval;

class TokenResponseAssembler
{
    private const EXPIRES_IN_RESERVE = 5 * 60;

    private const REQUIRED_FIELDS = [
        'access_token',
        'refresh_token',
        'expires_in',
    ];

    public function __construct(
        private AlfaBusinessLogger $logger,
    ) {
    }

    /**
     * @throws AlfaBusinessAccessTokenErrorException
     */
    public function create(array $response): TokenResponse
    {
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!array_key_exists($field, $response)) {
                $this->logger->error("$field field not found in access token request data", [
                    'response' => $response,
                ]);
                throw new AlfaBusinessAccessTokenErrorException();
            }
        }

        // если информация закеширована не нужно обновлять даты
        $expiresIn = $response['expires_in'];
        if (!array_key_exists('date', $response)) {
            $response['date'] = new \DateTime();

            if (!array_key_exists('expires_date', $response)) {
                $expiresDate = clone $response['date'];
                $expiresIn -= self::EXPIRES_IN_RESERVE;
                $expiresDate->add(new DateInterval('PT' . $expiresIn . 'S'));
                $response['expires_date'] = $expiresDate;
            }
        }

        return new TokenResponse(
            accessToken: (string) $response['access_token'],
            refreshToken: (string) $response['refresh_token'],
            expiresIn: $response['expires_in'], // @phan-suppress-current-line PhanPartialTypeMismatchArgument
            date: $response['date'],
            expiresDate: $response['expires_date'],
        );
    }
}
