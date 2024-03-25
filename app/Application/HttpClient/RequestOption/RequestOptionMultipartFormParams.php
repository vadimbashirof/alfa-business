<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

final class RequestOptionMultipartFormParams extends AbstractRequestOption
{
    /**
     * @param array<int, array<string, mixed>> $value
     *
     * multipart: (array) Array of associative arrays, each containing a
     * required "name" key mapping to the form field, name, a required
     * "contents" key mapping to a StreamInterface|resource|string, an
     * optional "headers" associative array of custom headers, and an
     * optional "filename" key mapping to a string to send as the filename in
     * the part. If no "filename" key is present, then no "filename" attribute
     * will be added to the part.
     */
    public function __construct(array $value)
    {
        $this->value = $value;
    }
}
