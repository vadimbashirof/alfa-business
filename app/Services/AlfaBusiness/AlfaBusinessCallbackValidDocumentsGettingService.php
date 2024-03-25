<?php

namespace App\Services\AlfaBusiness;

use App\Services\AlfaBusiness\Assembler\DocumentDTOAssembler;
use App\Services\AlfaBusiness\DTO\DocumentDTO;
use App\Services\AlfaBusiness\DTO\TransactionDTO;

class AlfaBusinessCallbackValidDocumentsGettingService
{
    public function __construct(
        private DocumentDTOAssembler $documentDTOAssembler,
        private AlfaBusinessTransactionValidationService $transactionValidationService,
        private AlfaBusinessDocumentValidationService $documentValidationService,
        private AlfaBusinessDocumentValidationErrorHandler $errorHandler,
    ) {
    }

    /**
     * @param TransactionDTO[] $callbackTransactions
     * @return DocumentDTO[]
     */
    public function get(array $callbackTransactions): array
    {
        $documents = [];
        foreach ($callbackTransactions as $callbackDTO) {
            $document = $this->documentDTOAssembler->create($callbackDTO);
            $transactionDTO = $document->getTransactionDTO();
            if ($transactionDTO->getDirection() === TransactionDTO::CREDIT_DIRECTION) {
                try {
                    $this->transactionValidationService->validate($document);
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
