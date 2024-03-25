<?php

namespace App\Services\AlfaBusiness\DTO;

use DateTime;

class TransactionDTO
{
    public const CREDIT_DIRECTION = 'CREDIT';

    public function __construct(
        private string $transactionId,
        private float $amount,
        private string $payerInn,
        private ?string $payerKpp,
        private string $payerAccount,
        private string $payeeAccount,
        private string $direction,
        private DateTime $documentDate,
        private DateTime $operationDate,
        private string $number,
        private string $paymentPurpose,
        private array $sourceFields = [],
    ) {
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getPayerInn(): string
    {
        return $this->payerInn;
    }

    public function getPayerKpp(): ?string
    {
        return $this->payerKpp;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function getDocumentDate(): DateTime
    {
        return $this->documentDate;
    }

    public function getOperationDate(): DateTime
    {
        return $this->operationDate;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getPayerAccount(): string
    {
        return $this->payerAccount;
    }

    public function getPayeeAccount(): string
    {
        return $this->payeeAccount;
    }

    public function getPaymentPurpose(): string
    {
        return $this->paymentPurpose;
    }

    public function getSourceFields(): array
    {
        return $this->sourceFields;
    }
}
