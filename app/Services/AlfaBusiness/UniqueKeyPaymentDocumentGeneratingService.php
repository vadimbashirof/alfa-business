<?php

namespace App\Services\AlfaBusiness;

use App\Services\AlfaBusiness\DTO\UniqueKeyPaymentDocumentDTO;

class UniqueKeyPaymentDocumentGeneratingService
{
    public function generate(UniqueKeyPaymentDocumentDTO $uniqueKeyPaymentDocument): string
    {
        $payDate = $uniqueKeyPaymentDocument->getPayDate();

        return md5(
            $uniqueKeyPaymentDocument->getParentPartnerId() .
            $uniqueKeyPaymentDocument->getPartnerId() .
            $uniqueKeyPaymentDocument->getNumber() .
            $uniqueKeyPaymentDocument->getInn() .
            $uniqueKeyPaymentDocument->getPaymentAmount() .
            $payDate->getTimestamp()
        );
    }
}
