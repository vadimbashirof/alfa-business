<?php

namespace App\Services\AlfaBusiness\DTO;

use Doctrine\Common\Collections\Collection;
use SD\Domain\PersistModel\Balance\Balance;
use SD\Domain\PersistModel\Balance\BalanceUploadLog;
use SD\Domain\PersistModel\Partner\Partner;
use SD\Domain\PersistModel\Partner\PartnerContract;

class DocumentDTO
{
    private ?\Throwable $exception = null;
    public function __construct(
        private TransactionDTO $transactionDTO,
        private ?Partner $parentPartner,
        private ?BalanceUploadLog $balanceUploadLog,
        private ?Collection $partnerContracts,
        private ?PartnerContract $mainContract,
        private ?Balance $balance,
        private ?Partner $partner,
    ) {
    }

    public function getTransactionDTO(): TransactionDTO
    {
        return $this->transactionDTO;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function getParentPartner(): ?Partner
    {
        return $this->parentPartner;
    }

    public function getBalanceUploadLog(): ?BalanceUploadLog
    {
        return $this->balanceUploadLog;
    }

    public function getPartnerContracts(): ?Collection
    {
        return $this->partnerContracts;
    }

    public function getMainContract(): ?PartnerContract
    {
        return $this->mainContract;
    }

    public function getBalance(): ?Balance
    {
        return $this->balance;
    }

    public function getException(): ?\Throwable
    {
        return $this->exception;
    }

    public function setException(\Throwable $exception): self
    {
        $this->exception = $exception;
        return $this;
    }
}
