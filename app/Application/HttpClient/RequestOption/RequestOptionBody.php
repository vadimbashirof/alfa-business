<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

final class RequestOptionBody extends AbstractRequestOption
{
    /**
     * @param mixed|resource|string|null|int|float|callable|\Iterator $value
     *
     * The body option is used to control the body of an entity enclosing request (e.g., PUT, POST, PATCH)
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
}
