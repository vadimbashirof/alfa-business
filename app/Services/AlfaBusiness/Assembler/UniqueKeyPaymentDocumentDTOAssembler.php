<?php

declare(strict_types=1);

namespace App\Services\AlfaBusiness\Assembler;

use App\Services\AlfaBusiness\DTO\TransactionDTO;
use App\Services\AlfaBusiness\DTO\UniqueKeyPaymentDocumentDTO;
use SD\Application\Service\Payment\PaymentFile\PaymentDocument;
use SD\Domain\PersistModel\Partner\Partner;

class UniqueKeyPaymentDocumentDTOAssembler
{
    public function createByPaymentDocument(
        Partner $parentPartner,
        PaymentDocument $paymentDocument,
        ?Partner $partner = null
    ): UniqueKeyPaymentDocumentDTO {
        $payDate = $paymentDocument->getPayDate();
        if (!$payDate) {
            throw new \RuntimeException('Pay date is required');
        }

        $dto = new UniqueKeyPaymentDocumentDTO(
            $parentPartner->getId(),
            (string)$paymentDocument->getPayerInn(),
            (string)$paymentDocument->getNumber(),
            $payDate,
            (float)$paymentDocument->getSum()
        );
        $dto->setPartnerId($partner?->getId());

        return $dto;
    }

    public function createByAlfaBusinessTransaction(
        TransactionDTO $paymentDocument,
        ?Partner $parentPartner,
        ?Partner $partner = null
    ): UniqueKeyPaymentDocumentDTO {
        $dto = new UniqueKeyPaymentDocumentDTO(
            $parentPartner ? $parentPartner->getId() : 0,
            $paymentDocument->getPayerInn(),
            $paymentDocument->getNumber(),
            $paymentDocument->getDocumentDate(),
            $paymentDocument->getAmount(),
        );
        $dto->setPartnerId($partner?->getId());

        return $dto;
    }
}
