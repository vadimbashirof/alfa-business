<?php

namespace App\Application\Exception;

interface ExceptionWithContextInterface extends \Throwable
{
    /**
     * @return array
     */
    public function getContext(): array;
}
