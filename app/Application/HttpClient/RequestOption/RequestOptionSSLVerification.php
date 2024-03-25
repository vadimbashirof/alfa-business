<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

final class RequestOptionSSLVerification extends AbstractRequestOption
{
    /**
     * @param bool $value
     *
     * Bool value for verify SSL certificate
     */
    public function __construct(bool $value)
    {
        $this->value = $value;
    }
}
