<?php

namespace App\Services\AlfaBusiness;

use App\Services\AlfaBusiness\Assembler\DocumentDTOAssembler;
use App\Services\AlfaBusiness\DTO\DocumentDTO;
use App\Services\AlfaBusiness\DTO\TransactionDTO;

class AlfaBusinessTransactionsValidDocumentsGettingService
{
    public function __construct(
        private DocumentDTOAssembler $documentDTOAssembler,
        private AlfaBusinessDocumentValidationService $documentValidationService,
        private AlfaBusinessDocumentValidationErrorHandler $errorHandler,
    ) {
    }

    /**
     * @param TransactionDTO[] $transactions
     * @return DocumentDTO[]
     */
    public function get(array $transactions): array
    {
        $documents = [];
        foreach ($transactions as $transaction) {
            $document = $this->documentDTOAssembler->create($transaction);
            $transactionDTO = $document->getTransactionDTO();
            if ($transactionDTO->getDirection() === TransactionDTO::CREDIT_DIRECTION) {
                try {
                    $this->documentValidationService->validate($document);
                } catch (\Throwable $exception) {
                    $document->setException($exception);
                    $this->errorHandler->handle($document, $exception);
                } finally {
                    $documents[] = $document;
                }
            }
        }
        return $documents;
    }
}
