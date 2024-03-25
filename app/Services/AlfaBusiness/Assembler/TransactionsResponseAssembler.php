<?php

namespace App\Services\AlfaBusiness\Assembler;

use App\Application\Logger\AlfaBusinessLogger;
use App\Services\AlfaBusiness\Response\TransactionResponse;
use DateTime;

class TransactionsResponseAssembler
{
    private const REQUIRED_FIELDS = [
        'transactionId',
        'rurTransfer',
        'amountRub',
        'number',
        'operationCode',
        'documentDate',
        'operationDate',
        'direction',
        'correspondingAccount',
    ];

    private const REQUIRED_RUR_TRANSFER_FIELDS = [
        'payerInn',
        'payerAccount',
        'payeeAccount',
    ];

    /**
     * @param AlfaBusinessLogger $logger
     */
    public function __construct(
        private AlfaBusinessLogger $logger,
    ) {
    }

    /**
     * @return TransactionResponse[]
     */
    public function create(?array $response): array
    {
        $result = [];
        if ($response) {
            $transactions = $response['transactions'] ?? [];
            if (!$transactions && !is_array($transactions)) {
                $this->logger->error("transactions field not found in transaction request data", [
                    'response' => $response,
                ]);
                return $result;
            }

            foreach ($transactions as $transaction) {
                foreach (self::REQUIRED_FIELDS as $field) {
                    if (!array_key_exists($field, $transaction)) {
                        $this->logger->error("$field field not found in transaction request data", [
                            'transaction' => $transaction,
                        ]);
                        continue 2;
                    }
                }

                foreach (self::REQUIRED_RUR_TRANSFER_FIELDS as $field) {
                    if (!array_key_exists($field, $transaction['rurTransfer'])) {
                        $this->logger->error("rurTransfer -> $field field not found in transaction request data", [
                            'transaction' => $transaction,
                        ]);
                        continue 2;
                    }
                }

                $result[] = new TransactionResponse(
                    transactionId: $transaction['transactionId'],
                    amount: (float) $transaction['amountRub']['amount'],
                    payerInn: $transaction['rurTransfer']['payerInn'],
                    payerKpp: $transaction['rurTransfer']['payerKpp'] ?? null,
                    payerAccount: $transaction['rurTransfer']['payerAccount'],
                    payeeAccount: $transaction['rurTransfer']['payeeAccount'],
                    direction: $transaction['direction'],
                    documentDate: new DateTime($transaction['documentDate']),
                    operationDate: new DateTime($transaction['operationDate']),
                    number: $transaction['number'],
                    paymentPurpose: $transaction['paymentPurpose'] ?? '',
                    sourceFields: $transaction, // @phan-suppress-current-line PhanPartialTypeMismatchArgument
                );
            }
        }

        return $result;
    }
}
