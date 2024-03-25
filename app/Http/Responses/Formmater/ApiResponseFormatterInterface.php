<?php

namespace App\Http\Response\Formatter;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

interface ApiResponseFormatterInterface
{
    /**
     * @param Request $request
     * @param SymfonyResponse $httpResponse
     * @param bool $success
     * @param string $code
     * @return SymfonyResponse
     */
    public function format(
        Request $request,
        SymfonyResponse $httpResponse,
        bool $success = true,
        string $code = '0'
    ): SymfonyResponse;
}
