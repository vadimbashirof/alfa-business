<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

final class RequestOptionJSON extends AbstractRequestOption
{
    /**
     * @param mixed $value
     *
     * Adds JSON data to a request. The provided value is JSON encoded
     * and a Content-Type header of application/json will be added to
     * the request if no Content-Type header is already present.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
}
