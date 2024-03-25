<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

final class RequestOptionSSLKey extends AbstractRequestOption
{
    /**
     * @param string $value
     *
     * Specify the path to a file containing a private SSL key in PEM format.
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
