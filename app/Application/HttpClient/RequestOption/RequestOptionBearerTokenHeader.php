<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

final class RequestOptionBearerTokenHeader extends AbstractRequestOption
{
    private string $bearerToken;

    /**
     * @param string $bearerToken
     */
    public function __construct(string $bearerToken)
    {
        $this->bearerToken = $bearerToken;
    }

    /**
     * @return array<string, string>
     */
    public function getValue()
    {
        return [
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ];
    }
}
