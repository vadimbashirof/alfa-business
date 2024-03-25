<?php

namespace App\Http\Middleware;

use App\Http\Response\Formatter\APIv3\Merchant\AlfaBusinessResponseFormatter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AlfaBusinessCallbackResponseMiddleware
{
    public function __construct(
        private AlfaBusinessResponseFormatter $responseFormatter,
    ) {
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);
        return $this->responseFormatter->format($request, $response);
    }
}
