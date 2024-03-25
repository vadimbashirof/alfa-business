<?php

namespace App\Application\Serializer;

interface SerializerInterface
{
    /**
     * @param mixed $value
     * @return string
     */
    public function serialize($value): string;

    /**
     * @param string $serializedValue
     * @return mixed
     */
    public function unserialize(string $serializedValue);
}
