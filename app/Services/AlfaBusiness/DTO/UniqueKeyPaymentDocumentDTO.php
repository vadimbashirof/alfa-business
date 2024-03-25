<?php

declare(strict_types=1);

namespace App\Services\AlfaBusiness\DTO;

class UniqueKeyPaymentDocumentDTO
{
    private ?int $partnerId = null;

    public function __construct(
        private int $parentPartnerId,
        private string $inn,
        private string $number,
        private \DateTime $payDate,
        private float $paymentAmount
    ) {
    }

    public function getPartnerId(): ?int
    {
        return $this->partnerId;
    }

    public function setPartnerId(?int $partnerId): UniqueKeyPaymentDocumentDTO
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    public function getPayDate(): \DateTime
    {
        return $this->payDate;
    }

    public function getInn(): string
    {
        return $this->inn;
    }

    public function getPaymentAmount(): float
    {
        return $this->paymentAmount;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getParentPartnerId(): int
    {
        return $this->parentPartnerId;
    }
}
