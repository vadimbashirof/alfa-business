<?php

namespace App\Application\Factory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

interface FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array<string, mixed> $options
     *
     * @return object
     *
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null);
}
