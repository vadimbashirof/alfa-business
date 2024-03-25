<?php

namespace App\Application\Redis;

use Redis;
use RedisException;

interface RedisInterface
{
    public const ATOMIC = 0;

    public const MULTI = 1;

    public const PIPELINE = 2;

    public const OPTION_READ_TIMEOUT = Redis::OPT_READ_TIMEOUT;

    /**
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @param $value
     * @param int|null $ttl
     * @return mixed
     */
    public function set($key, $value, $ttl = null);

    /**
     * @param $key
     * @param $ttl
     * @param $value
     * @return mixed
     */
    public function setex($key, $ttl, $value);

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setnx($key, $value);

    /**
     * @param $key
     * @return int
     */
    public function del($key);

    /**
     * @param $key
     * @return bool
     */
    public function exists($key): bool;

    /**
     * @param $key
     * @param $ttl
     * @return mixed
     */
    public function expire($key, $ttl);

    /**
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public function pExpire(string $key, int $ttl): bool;

    /**
     * @param $key
     * @return mixed
     */
    public function ttl($key): int;

    /**
     * @param $key
     * @param $timestamp
     * @return bool
     */
    public function expireAt($key, $timestamp);

    /**
     * @param $key
     * @return mixed
     */
    public function incr($key);

    /**
     * @param $key
     * @param $increment
     * @return mixed
     */
    public function incrByFloat($key, $increment);

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function incrBy($key, $value);

    /**
     * @param $key
     * @return mixed
     */
    public function decr($key);

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function decrBy($key, $value);

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function getSet($key, $value);

    /**
     * @param array $array
     * @return mixed
     */
    public function mget(array $array);

    /**
     * @param array $array
     * @return mixed
     */
    public function mset(array $array);

    /**
     * @param $mode
     * @return mixed
     */
    public function multi(int $mode);

    /**
     * @return mixed
     */
    public function exec();

    /**
     * @return mixed
     */
    public function discard();

    /**
     * @param string $key
     * @param string $hashKey
     * @param string $value
     * @return bool|int
     */
    public function hSet($key, $hashKey, $value);

    /**
     * @param $key
     * @param $hashKey
     * @param $value
     * @return mixed
     */
    public function hSetNx($key, $hashKey, $value);

    /**
     * @param $key
     * @param $hashKey
     * @return string|false
     */
    public function hGet($key, $hashKey);

    /**
     * @param $key
     * @return mixed
     */
    public function hLen($key);

    /**
     * @param $key
     * @param $hashKey
     * @return mixed
     */
    public function hDel($key, $hashKey);

    /**
     * @param $key
     * @return mixed
     */
    public function hKeys($key);

    /**
     * @param $key
     * @return mixed
     */
    public function hVals($key);

    /**
     * @param $key
     * @return array
     */
    public function hGetAll($key): array;

    /**
     * @param $key
     * @param $hashKey
     * @return mixed
     */
    public function hExists($key, $hashKey);

    /**
     * @param $key
     * @param $hashKey
     * @param $value
     * @return int
     */
    public function hIncrBy($key, $hashKey, $value): int;

    /**
     * @param $key
     * @param $field
     * @param $increment
     * @return mixed
     */
    public function hIncrByFloat($key, $field, $increment);

    /**
     * @param $key
     * @param $hashKeys
     * @return mixed
     */
    public function hMSet($key, $hashKeys);

    /**
     * @param $key
     * @param $hashKeys
     * @return mixed
     */
    public function hMGet($key, $hashKeys);

    /**
     * @param $pattern
     * @return array
     */
    public function keys($pattern): array;

    /**
     * @return string|null
     */
    public function getLastError(): ?string;

    /**
     * @param $key
     * @return void
     */
    public function watch($key): void;

    /**
     * unwatch all watched parameters
     * @return void
     */
    public function unwatch(): void;

    /**
     * @param $iterator
     * @param string|null $pattern
     * @param int $count
     * @return mixed
     */
    public function scan(&$iterator, string $pattern = null, int $count = 0);

    /**
     * @param string[] $channels
     * @param callable $callback
     * @return mixed
     * @throws RedisException
     */
    public function subscribe(array $channels, callable $callback);

    /**
     * @param array $channels
     * @return mixed
     */
    public function unsubscribe(array $channels);

    /**
     * @param string $channel
     * @param string $message
     * @return int
     */
    public function publish(string $channel, string $message): int;

    /**
     * @return bool
     */
    public function close(): bool;

    /**
     * Connect to server with current options
     * @return bool
     */
    public function reconnect(): bool;

    /**
     * @param int $option
     * @param mixed $value
     * @return bool
     */
    public function setOption(int $option, $value): bool;

    /**
     * @return bool
     */
    public function flushAll(): bool;

    /**
     * @param $key
     * @param ...$value1
     *
     * @return bool|int|mixed|\Redis
     */
    public function sAdd($key, ...$value1);

    /**
     * @param $key
     *
     * @return array|mixed|\Redis
     */
    public function sMembers($key);
}
