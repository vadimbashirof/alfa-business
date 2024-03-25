<?php

namespace App\Services\AlfaBusiness;

use App\Infrastructure\Logger\AlfaBusinessLogger;
use App\Services\AlfaBusiness\DTO\DocumentDTO;
use Doctrine\ORM\EntityManager;
use SD\Application\Service\Payment\Assembler\UniqueKeyPaymentDocumentDTOAssembler;
use SD\Application\Service\Payment\PaymentFile\ChangesPartnerBalanceDTOAssembler;
use SD\Application\Service\Payment\PaymentFile\UniqueKeyPaymentDocumentGeneratingService;
use SD\Service\Balance\PartnerBalanceChangingService;

class AlfaBusinessDocumentsHandler
{
    public function __construct(
        private ChangesPartnerBalanceDTOAssembler $changesPartnerBalanceDTOAssembler,
        private PartnerBalanceChangingService $partnerBalanceChangingService,
        private EntityManager $entityManager,
        private AlfaBusinessLogger $logger,
        private UniqueKeyPaymentDocumentGeneratingService $uniqueKeyPaymentDocumentGeneratingService,
        private UniqueKeyPaymentDocumentDTOAssembler $uniqueKeyPaymentDocumentDTOAssembler,
    ) {
    }

    /**
     * @param DocumentDTO[] $documentsDTO
     * @return void
     */
    public function handle(array $documentsDTO): void
    {
        foreach ($documentsDTO as $documentDTO) {
            $exception = $documentDTO->getException();
            if ($exception !== null) {
                continue;
            }

            $callbackDTO = $documentDTO->getTransactionDTO();

            $parentPartner = $documentDTO->getParentPartner();
            $partner = $documentDTO->getPartner();
            $balanceUploadLog = $documentDTO->getBalanceUploadLog();
            $balance = $documentDTO->getBalance();
            if (!$parentPartner || !$partner || !$balanceUploadLog || !$balance) {
                continue;
            }

            $this->entityManager->persist($balanceUploadLog);

            $transactionDTO = $documentDTO->getTransactionDTO();
            $sum = $transactionDTO->getAmount();

            $changesPartnerBalanceDTO = $this->changesPartnerBalanceDTOAssembler->create(
                abs($sum),
                $balanceUploadLog->getId(),
                $documentDTO->getMainContract(),
                $callbackDTO->getNumber(),
                $callbackDTO->getPaymentPurpose(),
            );

            $balance = $this->partnerBalanceChangingService->addFunds(
                $balance->getId(),
                $changesPartnerBalanceDTO
            );

            $uniqueKeyDTO = $this->uniqueKeyPaymentDocumentDTOAssembler->createByAlfaBusinessTransaction(
                $callbackDTO,
                $parentPartner,
                $partner
            );
            $documentUniqueKey =  $this->uniqueKeyPaymentDocumentGeneratingService->generate($uniqueKeyDTO);

            $this->logger->notice("Update partner balance {$partner->getCode()}", [
                'sum' => abs($sum),
                'uniqueKey' => $documentUniqueKey,
                'balanceUploadLogId' => $balanceUploadLog->getId(),
                'mainContractId' => $documentDTO->getMainContract()?->getId(),
                'balanceId' => $balance->getId(),
                'partnerCode' => $partner->getCode(),
                'parentPartnerCode' => $parentPartner->getCode(),
                'payerInn'    => $callbackDTO->getPayerInn(),
            ]);
        }

        $this->entityManager->flush();
    }
}
