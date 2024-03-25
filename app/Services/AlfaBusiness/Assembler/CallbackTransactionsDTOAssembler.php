<?php

namespace App\Services\AlfaBusiness\Assembler;

use App\Http\Requests\AlfaBusinessCallbackRequest;
use App\Services\AlfaBusiness\DTO\TransactionDTO;
use App\Services\AlfaBusiness\Exception\AlfaBusinessCallbackErrorException;
use DateTime;
use Illuminate\Http\Request;

class CallbackTransactionsDTOAssembler
{
    /**
     * @return TransactionDTO[]
     */
    public function create(AlfaBusinessCallbackRequest|Request $request): array
    {
        $result = [];

        $requestArray = $request->all();

        if (!$requestArray) {
            throw new AlfaBusinessCallbackErrorException('Alfa business bank callback data empty');
        }

        foreach ($requestArray['transactions'] as $item) {
            $item = $item['data'];
            $callbackDTO = new TransactionDTO(
                transactionId: $item['transactionId'],
                amount: abs((float) $item['amountRub']['amount']),
                payerInn: $item['rurTransfer']['payerInn'],
                payerKpp: $item['rurTransfer']['payerKpp'] ?? null,
                payerAccount: $item['rurTransfer']['payerAccount'],
                payeeAccount: $item['rurTransfer']['payeeAccount'],
                direction: $item['direction'],
                documentDate: new DateTime($item['documentDate']),
                operationDate: new DateTime($item['operationDate']),
                number: $item['number'],
                paymentPurpose: $item['paymentPurpose'] ?? '',
                sourceFields: $item,
            );
            $result[] = $callbackDTO;
        }

        return $result;
    }
}
