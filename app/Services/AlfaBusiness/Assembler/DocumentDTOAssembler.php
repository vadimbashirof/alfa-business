<?php

namespace App\Services\AlfaBusiness\Assembler;

use App\Services\AlfaBusiness\AlfaBusinessBalanceLogGettingService;
use App\Services\AlfaBusiness\DTO\DocumentDTO;
use App\Services\AlfaBusiness\DTO\TransactionDTO;
use SD\Domain\PersistModel\Balance\BalanceUploadLog;
use SD\Domain\PersistModel\Partner\Partner;
use SD\Domain\PersistModel\Partner\PartnerInfoRepository;
use SD\Domain\PersistModel\Partner\PartnerRepository;
use SD\Domain\Service\Balance\PartnerBalanceGettingService;
use SD\Domain\Service\Partner\MainPartnerContractGettingService;

class DocumentDTOAssembler
{
    protected const PARENT_PARTNER_CODE = 'MYAT';
    public function __construct(
        private PartnerInfoRepository $partnerInfoRepository,
        private MainPartnerContractGettingService $mainPartnerContractGettingService,
        private PartnerBalanceGettingService $partnerBalanceGettingService,
        private AlfaBusinessBalanceLogGettingService $alfaBusinessBalanceLogAssembler,
        private PartnerRepository $partnerRepository,
    ) {
    }

    public function create(TransactionDTO $transactionDTO): DocumentDTO
    {
        $parentPartner = $this->partnerRepository->findOneBy(['code' => self::PARENT_PARTNER_CODE]);
        $partner = $this->getPartner($transactionDTO);
        $partnerContracts = null;
        $mainContract = null;
        $balance = null;
        $balanceUploadLog = null;

        if ($partner) {
            $partnerContracts = $partner->getPartnerContracts();
            $mainContract = $this->mainPartnerContractGettingService->getByPartner($partner);
            $balance = $this->partnerBalanceGettingService->getBalance($partner);
        }

        if ($parentPartner && $partner) {
            $balanceUploadLog = $this->alfaBusinessBalanceLogAssembler->create(
                parentPartner: $parentPartner,
                callbackDTO: $transactionDTO,
                uploadStatus: BalanceUploadLog::UPLOAD_TYPE_SUCCESS,
                partner: $partner,
            );
        }

        return new DocumentDTO(
            transactionDTO: $transactionDTO,
            parentPartner: $parentPartner,
            balanceUploadLog: $balanceUploadLog,
            partnerContracts: $partnerContracts,
            mainContract: $mainContract,
            balance: $balance,
            partner: $partner,
        );
    }

    private function getPartner(TransactionDTO $callbackDTO): ?Partner
    {
        $partnerInfoList = $this->partnerInfoRepository->findByInfoAttributes([
            'inn' => $callbackDTO->getPayerInn(),
        ]);
        if ($partnerInfoList) {
            foreach ($partnerInfoList as $partnerInfo) {
                $partner = $partnerInfo->getPartner();
                if (!$partner->getParentPartner()) {
                    continue;
                }
                return $partner;
            }
        }

        return null;
    }
}
