<?php

namespace App\Services\AlfaBusiness;

use App\Infrastructure\Logger\AlfaBusinessLogger;
use App\Services\AlfaBusiness\Assembler\UniqueKeyPaymentDocumentDTOAssembler;
use App\Services\AlfaBusiness\DTO\DocumentDTO;


class AlfaBusinessDocumentValidationErrorHandler
{
    public function __construct(
        private AlfaBusinessLogger $logger,
        private UniqueKeyPaymentDocumentGeneratingService $uniqueKeyPaymentDocumentGeneratingService,
        private UniqueKeyPaymentDocumentDTOAssembler $uniqueKeyPaymentDocumentDTOAssembler,
    ) {
    }

    public function handle(DocumentDTO $document, \Throwable $exception): void
    {
        $transaction = $document->getTransactionDTO();
        $parentPartnerCode = $document->getParentPartner()?->getCode();
        $partnerCode = $document->getPartner()?->getCode();

        $mainContractId = $document->getMainContract()?->getId();
        $mainContractParentPartnerId = $document->getMainContract()?->getParentPartner()?->getId();

        $uniqueKeyDTO = $this->uniqueKeyPaymentDocumentDTOAssembler->createByAlfaBusinessTransaction(
            $transaction,
            $document->getParentPartner(),
            $document->getPartner()
        );
        $documentUniqueKey =  $this->uniqueKeyPaymentDocumentGeneratingService->generate($uniqueKeyDTO);

        $this->logger->error($exception->getMessage(), [
            'uniqueKey' => $documentUniqueKey,
            'parentPartner' => $parentPartnerCode,
            'partnerCode' => $partnerCode,
            'mainContractId' => $mainContractId,
            'mainContractParentPartnerId' => $mainContractParentPartnerId,
            'data' => $transaction->getSourceFields(),
        ]);
    }
}
