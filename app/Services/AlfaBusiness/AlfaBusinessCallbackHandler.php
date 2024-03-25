<?php

namespace App\Services\AlfaBusiness;

use App\Services\AlfaBusiness\DTO\DocumentDTO;
use App\Services\AlfaBusiness\DTO\TransactionDTO;
use App\Services\AlfaBusiness\Exception\AlfaBusinessCallbackErrorException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class AlfaBusinessCallbackHandler
{
    private string $environment;
    private array $ipWhiteList;

    public function __construct(
        string $environment,
        array $ipWhiteList,
        private AlfaBusinessCallbackValidDocumentsGettingService $validDocumentsGettingService,
        private AlfaBusinessDocumentsHandler $documentsHandler,
    ) {
        $this->environment = $environment;
        $this->ipWhiteList = $ipWhiteList;
    }

    /**
     * @param TransactionDTO[] $callbackTransactions
     * @return DocumentDTO[]
     */
    public function handle(array $callbackTransactions, Request $request): array
    {
        $ip = $request->ip();
        if (!$this->isIpAllowed($ip)) {
            throw new AlfaBusinessCallbackErrorException("Alfa business callback ip not allowed");
        }

        $documentsDTO = $this->validDocumentsGettingService->get($callbackTransactions);
        $this->documentsHandler->handle($documentsDTO);

        return $documentsDTO;
    }

    private function isIpAllowed(?string $ip): bool
    {
        if ($this->environment === AlfaBusinessHttpClient::TEST_ENV) {
            return true;
        }
        if (!$ip) {
            throw new AlfaBusinessCallbackErrorException('Alfa callback ip not found');
        }
        return IpUtils::checkIp($ip, $this->ipWhiteList);
    }
}
