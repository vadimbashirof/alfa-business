<?php

namespace App\Providers;

use App\Application\Config\ConfigService;
use App\Application\HttpClient\HttpClientInterface;
use App\Application\Logger\AlfaBusinessLogger;
use App\Application\Redis\RedisInterface;
use App\Application\Serializer\SerializerInterface;
use App\Services\AlfaBusiness\AlfaBusinessAuthConfigService;
use App\Services\AlfaBusiness\AlfaBusinessAuthService;
use App\Services\AlfaBusiness\AlfaBusinessCallbackHandler;
use App\Services\AlfaBusiness\AlfaBusinessCallbackValidDocumentsGettingService;
use App\Services\AlfaBusiness\AlfaBusinessDocumentsHandler;
use App\Services\AlfaBusiness\AlfaBusinessHttpClient;
use App\Services\AlfaBusiness\AlfaBusinessIDAuthService;
use App\Services\AlfaBusiness\Assembler\TokenResponseAssembler;
use App\Services\AlfaBusiness\Specification\IsNeedRefreshTokenSpecification;
use Illuminate\Support\ServiceProvider;

class AlfaBusinessServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AlfaBusinessHttpClient::class, function ($app) {
            $configService = $app->get(ConfigService::class);
            $config = $configService->get('services/alfa-business-config');

            $config['key_file'] = base_path() . '/config/services/certificates/alfa-business/key.pem';
            $config['cert_file'] = base_path() . '/config/services/certificates/alfa-business/cert.pem';

            return new AlfaBusinessHttpClient(
                $config,
                $app->get(HttpClientInterface::class),
                $app->get(AlfaBusinessLogger::class),
            );
        });

        $this->app->singleton(AlfaBusinessAuthService::class, function ($app) {
            $configService = $app->get(ConfigService::class);
            $config = $configService->get('services/alfa-business-config');

            return new AlfaBusinessAuthService(
                $config['code_request_state'],
                $app->get(RedisInterface::class),
                $app->get(SerializerInterface::class),
                $app->get(TokenResponseAssembler::class),
                $app->get(IsNeedRefreshTokenSpecification::class),
                $app->get(AlfaBusinessHttpClient::class),
                $app->get(AlfaBusinessAuthConfigService::class),
                $app->get(AlfaBusinessLogger::class),
            );
        });

        $this->app->singleton(AlfaBusinessCallbackHandler::class, function ($app) {
            $configService = $app->get(ConfigService::class);
            $config = $configService->get('services/alfa-business-config');

            return new AlfaBusinessCallbackHandler(
                $config['environment'],
                $config['ip_white_list'],
                $app->get(AlfaBusinessCallbackValidDocumentsGettingService::class),
                $app->get(AlfaBusinessDocumentsHandler::class),
            );
        });

        $this->app->singleton(AlfaBusinessIDAuthService::class, function ($app) {
            $configService = $app->get(ConfigService::class);
            $config = $configService->get('services/alfa-business-config');
            return new AlfaBusinessIDAuthService($config);
        });
    }
}

