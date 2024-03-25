<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

final class RequestOptionHeaders extends AbstractRequestOption
{
    /**
     * @param array<string, string|array<string>> $value
     *
     * Associative array of HTTP headers. Each value MUST be a string or array of strings.
     */
    public function __construct(array $value)
    {
        $this->value = $value;
    }
}
