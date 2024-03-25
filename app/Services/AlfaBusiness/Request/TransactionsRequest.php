<?php

namespace App\Services\AlfaBusiness\Request;

use App\Application\HttpClient\HttpClientInterface;

class TransactionsRequest extends Request
{
    private const ENDPOINT = '/api/statement/transactions';

    private const METHOD = HttpClientInterface::METHOD_GET;

    public function __construct(string $bearer, string $accountNumber, \DateTime $statementDate)
    {
        parent::__construct(
            endpoint: self::ENDPOINT,
            accept: 'application/json',
            contentType: 'application/json',
            method: self::METHOD,
            params: [
                'accountNumber' => $accountNumber,
                'statementDate' => $statementDate->format('Y-m-d'),
            ],
            isBearer: true,
            bearer: $bearer,
        );
    }
}
