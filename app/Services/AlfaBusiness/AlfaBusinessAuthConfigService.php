<?php

namespace App\Services\AlfaBusiness;

use App\Services\AlfaBusiness\Exception\AlfaBusinessAccessTokenErrorException;
use App\Services\AlfaBusiness\Response\TokenResponse;
use App\Services\Partner\ConfigUpdating\PartnerConfigUpdatingService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use SD\Application\Service\Partner\Exception\PartnerNotFoundException;
use SD\Domain\PersistModel\Config\Structure\Partner\Merchants\Elements\AlfaBusinessConfigElement;
use SD\Domain\PersistModel\Partner\PartnerRepository;

class AlfaBusinessAuthConfigService
{
    private const INITIATOR = 'Alfa Business Auth Service';
    private const PARTNER_CODE = 'MYAT';

    public function __construct(
        private PartnerRepository $partnerRepository,
        private PartnerConfigUpdatingService $partnerConfigUpdatingService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws PartnerNotFoundException
     */
    public function setToken(TokenResponse $accessTokenDTO): void
    {
        $partner = $this->partnerRepository->findOneBy(['code' => self::PARTNER_CODE]);
        if (!$partner) {
            throw new AlfaBusinessAccessTokenErrorException("Partner has not been found by code " . self::PARTNER_CODE);
        }

        $config = [
            'access_token' => $accessTokenDTO->getAccessToken(),
            'refresh_token' => $accessTokenDTO->getRefreshToken(),
            'expires_in' => $accessTokenDTO->getExpiresIn(),
            'date' => $accessTokenDTO->getDate()->format('Y-m-d H:i:s'),
            'expires_date' => $accessTokenDTO->getExpiresDate()->format('Y-m-d H:i:s'),
        ];

        $this->partnerConfigUpdatingService->update(
            self::INITIATOR,
            $partner,
            AlfaBusinessConfigElement::TYPE,
            $config
        );

        $this->entityManager->flush();
    }

    /**
     * @throws PartnerNotFoundException
     */
    public function getToken(): TokenResponse
    {
        $partner = $this->partnerRepository->findOneBy(['code' => self::PARTNER_CODE]);

        if (!$partner) {
            throw new PartnerNotFoundException("Partner has not been found by code " . self::PARTNER_CODE);
        }

        $config = $partner->getConfigObject();

        if (!isset($config->alfaBusiness)) {
            throw new RuntimeException('Параметр alfaBusiness не найден в конфиге партнера');
        }

        $alfaConfig = $config->alfaBusiness;

        if (!isset($alfaConfig->access_token)) {
            throw new AlfaBusinessAccessTokenErrorException('Параметр access_token не найден в конфиге партнера');
        }
        if (!isset($alfaConfig->refresh_token)) {
            throw new AlfaBusinessAccessTokenErrorException('Параметр refresh_token не найден в конфиге партнера');
        }
        if (!isset($alfaConfig->expires_in)) {
            throw new AlfaBusinessAccessTokenErrorException('Параметр expires_in не найден в конфиге партнера');
        }
        if (!isset($alfaConfig->date)) {
            throw new AlfaBusinessAccessTokenErrorException('Параметр date не найден в конфиге партнера');
        }
        if (!isset($alfaConfig->expires_date)) {
            throw new AlfaBusinessAccessTokenErrorException('Параметр expires_date не найден в конфиге партнера');
        }

        return new TokenResponse(
            accessToken: $alfaConfig->access_token,
            refreshToken: $alfaConfig->refresh_token,
            expiresIn: $alfaConfig->expires_in,
            date: new DateTime($alfaConfig->date),
            expiresDate: new DateTime($alfaConfig->expires_date),
        );
    }
}
