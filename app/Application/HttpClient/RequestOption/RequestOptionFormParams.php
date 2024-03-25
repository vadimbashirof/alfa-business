<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

final class RequestOptionFormParams extends AbstractRequestOption
{
    /**
     * @param array<string, string> $value
     *
     * Associative array of form field names to values
     * where each value is a string or array of strings. Sets the Content-Type
     * header to application/x-www-form-urlencoded when no Content-Type header
     * is already present.
     */
    public function __construct(array $value)
    {
        $this->value = $value;
    }
}
