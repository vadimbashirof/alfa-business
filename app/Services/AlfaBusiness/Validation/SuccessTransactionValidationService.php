<?php

declare(strict_types=1);

namespace App\Services\AlfaBusiness\Validation;

use App\Services\AlfaBusiness\DTO\TransactionDTO;
use App\Services\AlfaBusiness\Exception\AlfaBusinessValidationNotFountTransactionException;
use App\Services\AlfaBusiness\Response\TransactionResponse;
use SD\Service\Logger\Service\AlfaLogger;

class SuccessTransactionValidationService
{
    public function __construct(private AlfaLogger $logger)
    {
    }

    /**
     * @param TransactionResponse[] $transactions
     * @throws AlfaBusinessValidationNotFountTransactionException
     */
    public function validate(array $transactions, TransactionDTO $callbackDTO): bool
    {
        foreach ($transactions as $transaction) {
            $this->validateTransaction($transaction, $callbackDTO);
        }

        return true;
    }

    /**
     * @throws AlfaBusinessValidationNotFountTransactionException
     */
    private function validateTransaction(TransactionResponse $transaction, TransactionDTO $callbackDTO): void
    {
        if ($transaction->getNumber() !== $callbackDTO->getNumber()) {
            $this->logCritical('number', $transaction, $callbackDTO);
            throw $this->makeError('number', $transaction->getNumber(), $callbackDTO->getNumber());
        }

        if ($transaction->getTransactionId() !== $callbackDTO->getTransactionId()) {
            $this->logCritical('transaction_id', $transaction, $callbackDTO);
            throw $this->makeError(
                'transaction_id',
                $transaction->getTransactionId(),
                $callbackDTO->getTransactionId()
            );
        }

        if ($transaction->getAmount() !== $callbackDTO->getAmount()) {
            $this->logCritical('amount', $transaction, $callbackDTO);
            throw $this->makeError('amount', (string) $transaction->getAmount(), (string) $callbackDTO->getAmount());
        }

        if ($transaction->getDocumentDate()->getTimestamp() !== $callbackDTO->getDocumentDate()->getTimestamp()) {
            $this->logCritical('number', $transaction, $callbackDTO);
            throw $this->makeError(
                'number',
                (string) $transaction->getDocumentDate()->getTimestamp(),
                (string) $callbackDTO->getDocumentDate()->getTimestamp()
            );
        }

        if ($transaction->getDirection() !== $callbackDTO->getDirection()) {
            $this->logCritical('direction', $transaction, $callbackDTO);
            throw $this->makeError('direction', $transaction->getDirection(), $callbackDTO->getDirection());
        }

        if ($transaction->getPayerInn() !== $callbackDTO->getPayerInn()) {
            $this->logCritical('payer_inn', $transaction, $callbackDTO);
            throw $this->makeError('payer_inn', $transaction->getPayerInn(), $callbackDTO->getPayerInn());
        }

        if ($transaction->getPayerAccount() !== $callbackDTO->getPayerAccount()) {
            $this->logCritical('payer_account', $transaction, $callbackDTO);
            throw $this->makeError('payer_account', $transaction->getPayerAccount(), $callbackDTO->getPayerAccount());
        }

        if ($transaction->getPayeeAccount() !== $callbackDTO->getPayeeAccount()) {
            $this->logCritical('payee_account', $transaction, $callbackDTO);
            throw $this->makeError('payee_account', $transaction->getPayeeAccount(), $callbackDTO->getPayeeAccount());
        }
    }

    private function logCritical(
        string $mismatchedField,
        TransactionResponse $transaction,
        TransactionDTO $callbackDTO
    ): void {
        $transactionArray = $this->makeTransactionArray($transaction);
        $callbackArray = $this->makeCallbackArray($callbackDTO);

        $this->logger->critical(
            'Alfa business callback transaction invalid.',
            [
                'mismatched_field' => $mismatchedField,
                'transaction' => $transactionArray,
                'callback' => $callbackArray,
            ]
        );
    }

    private function makeTransactionArray(TransactionResponse $transaction): array
    {
        return [
            'number' => $transaction->getNumber(),
            'transaction_id' => $transaction->getTransactionId(),
            'amount' => $transaction->getAmount(),
            'document_timestamp' => $transaction->getDocumentDate()->getTimestamp(),
            'direction' => $transaction->getDirection(),
            'payerInn' => $transaction->getPayerInn(),
            'payer_account' => $transaction->getPayerAccount(),
            'payee_account' => $transaction->getPayeeAccount(),
        ];
    }

    private function makeCallbackArray(TransactionDTO $transaction): array
    {
        return [
            'number' => $transaction->getNumber(),
            'transaction_id' => $transaction->getTransactionId(),
            'amount' => $transaction->getAmount(),
            'document_timestamp' => $transaction->getDocumentDate()->getTimestamp(),
            'direction' => $transaction->getDirection(),
            'payerInn' => $transaction->getPayerInn(),
            'payer_account' => $transaction->getPayerAccount(),
            'payee_account' => $transaction->getPayeeAccount(),
        ];
    }

    private function makeError(
        string $field,
        string $transactionValue,
        string $callbackValue
    ): AlfaBusinessValidationNotFountTransactionException {
        return new AlfaBusinessValidationNotFountTransactionException(
            "Alfa business callback transaction invalid. Transaction $field value: $transactionValue,
                                    doesn't match callback value:" . $callbackValue
        );
    }
}
