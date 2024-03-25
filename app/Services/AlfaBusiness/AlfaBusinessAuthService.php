<?php

namespace App\Services\AlfaBusiness;

use App\Application\Logger\AlfaBusinessLogger;
use App\Application\Redis\RedisInterface;
use App\Application\Serializer\SerializerInterface;
use App\Http\Requests\AlfaBusinessSetAccessTokenRequest;
use App\Services\AlfaBusiness\Assembler\TokenResponseAssembler;
use App\Services\AlfaBusiness\Exception\AlfaBusinessAccessTokenErrorException;
use App\Services\AlfaBusiness\Exception\AlfaBusinessHttpClientException;
use App\Services\AlfaBusiness\Request\TokenRefreshRequest;
use App\Services\AlfaBusiness\Request\TokenRequest;
use App\Services\AlfaBusiness\Response\TokenResponse;
use App\Services\AlfaBusiness\Specification\IsNeedRefreshTokenSpecification;

class AlfaBusinessAuthService
{
    private const ACCESS_TOKEN_KEY = 'alfa-business-access-token';

    public function __construct(
        private string $codeRequestState,
        private RedisInterface $redis,
        private SerializerInterface $serializer,
        private TokenResponseAssembler $accessTokenDTOAssembler,
        private IsNeedRefreshTokenSpecification $isNeedRefreshAlfaAccessTokenSpecification,
        private AlfaBusinessHttpClient $alfaClientService,
        private AlfaBusinessAuthConfigService $authConfigService,
        private AlfaBusinessLogger $logger,
    ) {
    }

    /**
     * @throws AlfaBusinessHttpClientException
     */
    public function setAccessToken(AlfaBusinessSetAccessTokenRequest $request): TokenResponse
    {
        if ($request->getState() !== $this->codeRequestState) {
            throw new AlfaBusinessAccessTokenErrorException('Alfa business client code request wrong state');
        }

        $this->logger->notice('Sending a request to receive access token from the alfa business bank');

        $requestDTO = new TokenRequest($request->getCode());

        $accessTokenResponse = $this->alfaClientService->sendRequest($requestDTO);
        $accessTokenDTO = $this->accessTokenDTOAssembler->create($accessTokenResponse);
        $this->saveTokenToCache($accessTokenDTO);
        $this->logger->notice('Alfa business set token data', $accessTokenDTO->getAttributes());
        $this->authConfigService->setToken($accessTokenDTO);

        return $accessTokenDTO;
    }

    public function getAccessToken(): string
    {
        $token = '';
        try {
            $tokenDTO = $this->getTokenFromCache();
            if ($this->isNeedRefreshAlfaAccessTokenSpecification->isSatisfiedBy($tokenDTO)) {
                $tokenDTO = $this->requestRefreshAccessToken($tokenDTO);
            }
            $token = $tokenDTO->getAccessToken();
        } catch (AlfaBusinessHttpClientException $exception) {
            throw new AlfaBusinessAccessTokenErrorException($exception->getMessage());
        }

        return $token;
    }

    /**
     * @throws AlfaBusinessHttpClientException
     */
    public function requestRefreshAccessToken(TokenResponse $accessTokenDTO): TokenResponse
    {
        $this->logger->notice('Sending a request to refresh token from alfa business bank');
        $requestDTO = new TokenRefreshRequest($accessTokenDTO->getRefreshToken());

        $accessTokenResponse = $this->alfaClientService->sendRequest($requestDTO);
        $accessTokenDTO = $this->accessTokenDTOAssembler->create($accessTokenResponse);
        $this->saveTokenToCache($accessTokenDTO);
        $this->authConfigService->setToken($accessTokenDTO);

        return $accessTokenDTO;
    }

    /**
     * @throws AlfaBusinessAccessTokenErrorException
     */
    public function getTokenFromCache(): TokenResponse
    {
        $accessTokenData = $this->redis->get(self::ACCESS_TOKEN_KEY);
        if (!$accessTokenData) {
            return $this->authConfigService->getToken();
        }
        $accessTokenData = $this->serializer->unserialize($accessTokenData);
        return $this->accessTokenDTOAssembler->create($accessTokenData);
    }

    private function saveTokenToCache(TokenResponse $accessTokenDTO): void
    {
        $response = $this->serializer->serialize($accessTokenDTO->getAttributes());
        $this->redis->set(self::ACCESS_TOKEN_KEY, $response);
    }
}
