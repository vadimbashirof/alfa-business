<?php

namespace App\Application\Serializer;


use RuntimeException;

final class ZlibCompressionDecorator implements SerializerInterface
{
    private SerializerInterface $decoratedSerializer;

    /**
     * @param SerializerInterface $decoratedSerializer
     */
    public function __construct(SerializerInterface $decoratedSerializer)
    {
        $this->decoratedSerializer = $decoratedSerializer;
    }

    /**
     * @inheritDoc
     */
    public function serialize($value): string
    {
        $serializedValue = $this->decoratedSerializer->serialize($value);

        $compressedValue = gzcompress($serializedValue, 6, ZLIB_ENCODING_DEFLATE);

        if ($compressedValue === false) {
            throw new RuntimeException('gzcompress() error');
        }

        return $compressedValue;
    }

    /**
     * @inheritDoc
     */
    public function unserialize(string $serializedValue)
    {
        $uncompressedValue = gzuncompress($serializedValue);

        if ($uncompressedValue === false) {
            throw new RuntimeException('gzuncompress() error');
        }

        return $this->decoratedSerializer->unserialize($uncompressedValue);
    }
}
