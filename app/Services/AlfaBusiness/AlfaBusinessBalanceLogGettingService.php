<?php

namespace App\Services\AlfaBusiness;

use App\Services\AlfaBusiness\DTO\TransactionDTO;
use SD\Application\Service\Payment\Assembler\UniqueKeyPaymentDocumentDTOAssembler;
use SD\Application\Service\Payment\PaymentFile\UniqueKeyPaymentDocumentGeneratingService;
use SD\Domain\PersistModel\Balance\BalanceUploadLog;
use SD\Domain\PersistModel\Partner\Partner;
use SD\Domain\Service\Balance\PartnerBalanceGettingService;
use TO\Logger\Tool\PIDGeneratorInterface;

class AlfaBusinessBalanceLogGettingService
{
    public function __construct(
        private PartnerBalanceGettingService $partnerBalanceGettingService,
        private PIDGeneratorInterface $pidGenerator,
        private UniqueKeyPaymentDocumentGeneratingService $uniqueKeyPaymentDocumentGeneratingService,
        private UniqueKeyPaymentDocumentDTOAssembler $uniqueKeyPaymentDocumentDTOAssembler,
    ) {
    }

    public function create(
        Partner $parentPartner,
        TransactionDTO $callbackDTO,
        string $uploadStatus,
        Partner $partner = null,
        string $errorType = null,
        bool $isNeedUniqueKey = true
    ): BalanceUploadLog {
        $paymentAmount = $callbackDTO->getAmount();
        $balanceStart = $balanceEnd = null;

        if ($partner) {
            $balance = $this->partnerBalanceGettingService->getBalance($partner);
            if ($balance) {
                $balance = $balance->getBalance();
                $balanceStart = $balance;
                $balanceEnd = $balance + $paymentAmount;
            } else {
                $uploadStatus = BalanceUploadLog::UPLOAD_TYPE_ERROR;
            }
        }

        $documentUniqueKey = null;
        if ($isNeedUniqueKey) {
            $uniqueKeyDTO = $this->uniqueKeyPaymentDocumentDTOAssembler->createByAlfaBusinessTransaction(
                $callbackDTO,
                $parentPartner,
                $partner
            );
            $documentUniqueKey =  $this->uniqueKeyPaymentDocumentGeneratingService->generate($uniqueKeyDTO);
        }

        $balanceUploadLog = new BalanceUploadLog(
            partner: $partner,
            payDate: $callbackDTO->getDocumentDate(),
            inn: $callbackDTO->getPayerInn(),
            balanceStart: $balanceStart,
            balanceEnd: $balanceEnd,
            paymentAmount: $paymentAmount,
            comment: $callbackDTO->getPaymentPurpose(),
            documentFields: $callbackDTO->getSourceFields(),
            uploadStatus: $uploadStatus,
            documentUniqueKey: $documentUniqueKey,
            number: $callbackDTO->getNumber(),
            parentPartner: $parentPartner,
            uploadSource: BalanceUploadLog::ALFA_BANK,
            pid: $this->pidGenerator->getPid(),
        );

        $balanceUploadLog->setErrorType($errorType);

        return $balanceUploadLog;
    }
}
