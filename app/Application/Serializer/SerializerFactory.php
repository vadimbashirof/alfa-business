<?php

namespace App\Application\Serializer;

use App\Application\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

class SerializerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param array|null $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SerializerInterface
    {
        $isCompressionEnabled = (bool)($options['compression_enabled'] ?? true);

        if (!extension_loaded('igbinary')) {
            throw new RuntimeException('The igbinary extension is required for serialization');
        }
        if ($isCompressionEnabled && !extension_loaded('zlib')) {
            throw new RuntimeException('The zlib extension is required for serialization with compression');
        }

        /** @var IgbinarySerializer $serializer */
        $serializer = $container->get(IgbinarySerializer::class);

        return $isCompressionEnabled
            ? new ZlibCompressionDecorator($serializer)
            : $serializer;
    }
}
