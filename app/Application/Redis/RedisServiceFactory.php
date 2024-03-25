<?php

namespace App\Application\Redis;

use App\Application\Config\ConfigService;
use App\Application\Factory\FactoryInterface;
use BL\Dsn;
use BL\Redis\RedisFactory;
use BL\Redis\RedisInterface;
use Psr\Container\ContainerInterface;

class RedisServiceFactory implements FactoryInterface
{
    public const DEFAULT_REDIS_CONFIG_NAME = 'redis';

    /**
     * @param string $requestedName
     * @param array|null $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RedisInterface
    {
        /** @var ConfigService $configService */
        $configService = $container->get(ConfigService::class);

        $redisConfigName = $options['redis-service-config'] ?? self::DEFAULT_REDIS_CONFIG_NAME;
        $redisConfig = $configService->getConfig(SD::LIB_SD, $redisConfigName);

        if ($redisConfigName !== self::DEFAULT_REDIS_CONFIG_NAME && empty($redisConfig)) {
            $redisConfig = $configService->getConfig(SD::LIB_SD, self::DEFAULT_REDIS_CONFIG_NAME);
        }

        if (isset($options['config']['persistentId'])) {
            $redisConfig['persistentId'] = $options['config']['persistentId'];
        }

        /** @var Dsn\Redis $dsn */
        $dsn = Dsn::create(Dsn::DRIVER_REDIS, $redisConfig);
        return RedisFactory::getInstance($dsn, $options['newInstance'] ?? false);
    }
}
