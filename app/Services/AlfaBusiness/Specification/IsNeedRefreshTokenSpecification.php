<?php

namespace App\Services\AlfaBusiness\Specification;

use App\Services\AlfaBusiness\Response\TokenResponse;

class IsNeedRefreshTokenSpecification
{
    public function isSatisfiedBy(TokenResponse $alfaAccessTokenDTO): bool
    {
        $nowDate = new \DateTime();
        if ($nowDate > $alfaAccessTokenDTO->getExpiresDate()) {
            return true;
        }
        return false;
    }
}
