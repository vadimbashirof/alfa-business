<?php

namespace App\Application\Serializer;

final class IgbinarySerializer implements SerializerInterface
{
    /**
     * @inheritDoc
     */
    public function serialize($value): string
    {
        return igbinary_serialize($value);
    }

    /**
     * @inheritDoc
     */
    public function unserialize(string $serializedValue)
    {
        return igbinary_unserialize($serializedValue);
    }
}
