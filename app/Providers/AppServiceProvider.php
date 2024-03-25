<?php

namespace App\Providers;

use App\Application\Config\ConfigService;
use App\Application\Config\ConfigServiceFactory;
use App\Application\Redis\RedisInterface;
use App\Application\Redis\RedisServiceFactory;
use App\Application\Serializer\SerializerFactory;
use App\Application\Serializer\SerializerInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        RedisInterface::class => RedisServiceFactory::class,
        SerializerInterface::class => SerializerFactory::class,
        ConfigService::class => ConfigServiceFactory::class,
    ];
}
