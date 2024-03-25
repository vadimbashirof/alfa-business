<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

final class RequestOptionCertificate extends AbstractRequestOption
{
    /**
     * @param string $value
     *
     * Set to a string to specify the path to a file containing a PEM formatted client side certificate.
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
