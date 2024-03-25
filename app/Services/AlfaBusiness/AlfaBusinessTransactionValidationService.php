<?php

namespace App\Services\AlfaBusiness;

use App\Services\AlfaBusiness\DTO\DocumentDTO;
use App\Services\AlfaBusiness\Exception\AlfaBusinessHttpClientException;
use App\Services\AlfaBusiness\Exception\AlfaBusinessValidationNotFountTransactionException;
use App\Services\AlfaBusiness\Validation\SuccessTransactionValidationService;

class AlfaBusinessTransactionValidationService
{
    public function __construct(
        private AlfaBusinessTransactionsGettingService $transactionsGettingService,
        private SuccessTransactionValidationService $successTransactionValidationService,
    ) {
    }

    /**
     * @throws AlfaBusinessHttpClientException
     * @throws AlfaBusinessValidationNotFountTransactionException
     */
    public function validate(DocumentDTO $documentDTO): void
    {
        $callbackTransactionDTO = $documentDTO->getTransactionDTO();
        $transactions = $this->transactionsGettingService->get(
            accountNumber: $callbackTransactionDTO->getPayeeAccount(),
            statementDate: $callbackTransactionDTO->getOperationDate(),
        );
        $this->successTransactionValidationService->validate($transactions, $callbackTransactionDTO);
    }
}
