<?php

namespace App\Services\AlfaBusiness\Response;

use DateTime;

class TokenResponse
{
    public function __construct(
        private string $accessToken,
        private string $refreshToken,
        private int $expiresIn,
        private DateTime $date,
        private DateTime $expiresDate,
    ) {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getExpiresDate(): DateTime
    {
        return $this->expiresDate;
    }

    public function getAttributes(): array
    {
        return [
            'access_token' => $this->getAccessToken(),
            'refresh_token' => $this->getRefreshToken(),
            'expires_in' => $this->getExpiresIn(),
            'date' => $this->getDate(),
            'expires_date' => $this->getExpiresDate(),
        ];
    }
}
