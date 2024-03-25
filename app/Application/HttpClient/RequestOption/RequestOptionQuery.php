<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

final class RequestOptionQuery extends AbstractRequestOption
{
    /**
     * @param array<string, string|int> $value
     *
     * Associative array of query string values to add to the request.
     * This option uses PHP's http_build_query() to create the string representation.
     */
    public function __construct(array $value)
    {
        $this->value = $value;
    }
}
