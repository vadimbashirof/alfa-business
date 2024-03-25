<?php

declare(strict_types=1);

namespace App\Application\HttpClient\RequestOption;

abstract class AbstractRequestOption
{
    /** @var mixed */
    protected $value;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
