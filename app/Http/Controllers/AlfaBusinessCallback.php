<?php

namespace App\Http\Controllers;

use App\Application\Logger\AlfaBusinessLogger;
use App\Http\Requests\AlfaBusinessCallbackRequest;
use App\Services\AlfaBusiness\AlfaBusinessCallbackHandler;
use App\Services\AlfaBusiness\Assembler\CallbackTransactionsDTOAssembler;

class AlfaBusinessCallback extends Controller
{
    public function handle(
        AlfaBusinessCallbackRequest $request,
        AlfaBusinessLogger $logger,
        CallbackTransactionsDTOAssembler $assembler,
        AlfaBusinessCallbackHandler $callbackHandler,
    ): array {
        $logger->notice("Alfa business callback process start", [
            'request' => $request->all(),
        ]);
        $callbackTransactionsDTO = $assembler->create($request);
        return $callbackHandler->handle($callbackTransactionsDTO, $request);
    }
}
