<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

final class RequestOptionTimeout extends AbstractRequestOption
{
    /**
     * @param float $value
     *
     * Float describing the timeout of the request in seconds.
     * Use 0 to wait indefinitely (the default behavior).
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }
}
