<?php

namespace App\Services\AlfaBusiness;

use App\Services\AlfaBusiness\DTO\DocumentDTO;
use App\Services\AlfaBusiness\DTO\TransactionDTO;
use App\Services\AlfaBusiness\Exception\AlfaBusinessValidationAlreadyUploadedException;
use App\Services\AlfaBusiness\Exception\AlfaBusinessValidationDifferentMainContractException;
use App\Services\AlfaBusiness\Exception\AlfaBusinessValidationDifferentParentPartnerException;
use App\Services\AlfaBusiness\Exception\AlfaBusinessValidationNotBalanceException;
use App\Services\AlfaBusiness\Exception\AlfaBusinessValidationNotFoundMainContractException;
use App\Services\AlfaBusiness\Exception\AlfaBusinessValidationNotFoundParentPartnerException;
use App\Services\AlfaBusiness\Exception\AlfaBusinessValidationNotFoundPartnerContractsException;
use App\Services\AlfaBusiness\Exception\AlfaBusinessValidationNotFoundPartnerException;
use Doctrine\ORM\EntityManager;
use SD\Application\Service\Payment\Assembler\UniqueKeyPaymentDocumentDTOAssembler;
use SD\Application\Service\Payment\PaymentFile\UniqueKeyPaymentDocumentGeneratingService;
use SD\Domain\PersistModel\Balance\BalanceUploadLog;
use SD\Domain\PersistModel\Balance\BalanceUploadLogRepository;
use SD\Domain\PersistModel\Partner\Partner;

class AlfaBusinessDocumentValidationService
{
    private ?Partner $parentPartner = null;
    private ?Partner $partner = null;

    public function __construct(
        private EntityManager $entityManager,
        private AlfaBusinessBalanceLogGettingService $alfaBusinessBalanceLogAssembler,
        private BalanceUploadLogRepository $balanceUploadLogRepository,
        private UniqueKeyPaymentDocumentGeneratingService $uniqueKeyPaymentDocumentGeneratingService,
        private UniqueKeyPaymentDocumentDTOAssembler $uniqueKeyPaymentDocumentDTOAssembler,
    ) {
    }

    public function validate(DocumentDTO $documentDTO): void
    {
        $transactionDTO = $documentDTO->getTransactionDTO();

        $this->parentPartner = $documentDTO->getParentPartner();
        if (!$this->parentPartner) {
            throw new AlfaBusinessValidationNotFoundParentPartnerException('Alfa business parent partner not found');
        }

        $uniqueKeyDTO = $this->uniqueKeyPaymentDocumentDTOAssembler->createByAlfaBusinessTransaction(
            $documentDTO->getTransactionDTO(),
            $this->parentPartner,
            $documentDTO->getPartner()
        );
        $uniqueKey =  $this->uniqueKeyPaymentDocumentGeneratingService->generate($uniqueKeyDTO);

        $alreadyUploaded = $this->balanceUploadLogRepository->findOneBy(['uniqueKey' => $uniqueKey]);
        if ($alreadyUploaded) {
            throw new AlfaBusinessValidationAlreadyUploadedException('Alfa business transaction has already been uploaded');
        }

        $this->partner = $documentDTO->getPartner();
        if (!$this->partner) {
            $this->addBalanceUploadLog($transactionDTO, BalanceUploadLog::ERROR_TYPE_PARTNER_NOT_FOUND);
            throw new AlfaBusinessValidationNotFoundPartnerException('Alfa business partner not found');
        }

        if ($this->parentPartner->getId() !== $this->partner->getParentPartner()?->getId()) {
            $this->addBalanceUploadLog($transactionDTO, BalanceUploadLog::ERROR_TYPE_PARTNER_NOT_FOUND);
            throw new AlfaBusinessValidationDifferentParentPartnerException('Alfa business partner not found');
        }

        $partnerContracts = $documentDTO->getPartnerContracts();
        if (!$partnerContracts) {
            $this->addBalanceUploadLog($transactionDTO, BalanceUploadLog::ERROR_TYPE_PARTNER_HAS_NO_CONTRACT);
            throw new AlfaBusinessValidationNotFoundPartnerContractsException('Alfa business partner contracts not found');
        }

        $mainContract = $documentDTO->getMainContract();
        if (!$mainContract) {
            $this->addBalanceUploadLog($transactionDTO, BalanceUploadLog::ERROR_TYPE_MAIN_CONTRACT_NOT_FOUND);
            throw new AlfaBusinessValidationNotFoundMainContractException('Alfa business main contract not found');
        }

        if ($mainContract->getParentPartner()->getId() !== $this->parentPartner->getId()) {
            $this->addBalanceUploadLog($transactionDTO, BalanceUploadLog::ERROR_TYPE_CASH_RECEIVED_ANOTHER_CONTRACT);
            throw new AlfaBusinessValidationDifferentMainContractException('Alfa business cash received another contract');
        }

        $balance = $documentDTO->getBalance();
        if (!$balance) {
            $this->addBalanceUploadLog($transactionDTO, BalanceUploadLog::ERROR_TYPE_BALANCE_NOT_FOUND);
            throw new AlfaBusinessValidationNotBalanceException('Alfa business partner balance not found');
        }
    }

    private function addBalanceUploadLog(TransactionDTO $callbackDTO, string $errorType, bool $isNeedUniqueKey = true): void
    {
        if (!$this->parentPartner) {
            return;
        }
        $balanceUploadLog = $this->alfaBusinessBalanceLogAssembler->create(
            parentPartner: $this->parentPartner,
            callbackDTO: $callbackDTO,
            uploadStatus: BalanceUploadLog::UPLOAD_TYPE_ERROR,
            partner: $this->partner,
            errorType: $errorType,
            isNeedUniqueKey: $isNeedUniqueKey,
        );
        $this->entityManager->persist($balanceUploadLog);
    }
}
