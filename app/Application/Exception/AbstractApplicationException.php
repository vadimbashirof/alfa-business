<?php

declare(strict_types=1);

namespace App\Application\Exception;

use Exception;
use Throwable;

abstract class AbstractApplicationException extends Exception implements ExceptionWithContextInterface
{
    protected array $context = [];

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message ?: $this->message, $code, $previous);
    }

    public function setContext(array $context): AbstractApplicationException
    {
        $this->context = $context;
        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}

