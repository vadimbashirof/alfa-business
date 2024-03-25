<?php

namespace App\Services\AlfaBusiness;

use App\Infrastructure\Logger\AlfaBusinessLogger;
use App\Services\AlfaBusiness\Assembler\TransactionsResponseAssembler;
use App\Services\AlfaBusiness\Exception\AlfaBusinessHttpClientException;
use App\Services\AlfaBusiness\Request\TransactionsRequest;
use App\Services\AlfaBusiness\Response\TransactionResponse;
use DateTime;

class AlfaBusinessTransactionsGettingService
{
    public function __construct(
        private AlfaBusinessAuthService $alfaBusinessAuthService,
        private AlfaBusinessHttpClient $alfaClientService,
        private TransactionsResponseAssembler $alfaBusinessTransactionDTOAssembler,
        private AlfaBusinessLogger $logger,
    ) {
    }

    /**
    * /**
     * @return TransactionResponse[]
     * @throws AlfaBusinessHttpClientException
     */
    public function get(string $accountNumber, DateTime $statementDate): array
    {
        $this->logger->notice('Sending a request to receive transactions from the alfa business bank', [
            'accountNumber' => $accountNumber,
            'statementDate' => $statementDate,
        ]);

        $bearer = $this->alfaBusinessAuthService->getAccessToken();
        $requestDOT = new TransactionsRequest(
            bearer: $bearer,
            accountNumber: $accountNumber,
            statementDate: $statementDate,
        );

        $transactionsResponse = $this->alfaClientService->sendRequest($requestDOT);

        return $this->alfaBusinessTransactionDTOAssembler->create($transactionsResponse);
    }
}
