<?php

namespace Chief\Decorators;

use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    /**
     * @var
     */
    private $key;
    /**
     * @var
     */
    private $value;
    /**
     * @var int
     */
    private $expiresAfter;

    /**
     * @param $key
     *  The cache item key.
     *
     * @param $value
     *  The value to cache.
     *
     * @param int $expiresAfter
     *  The period of time from the present after which the item MUST be considered
     *  expired. An integer parameter is understood to be the time in seconds until
     *  expiration.
     */
    public function __construct($key, $value, $expiresAfter)
    {

        $this->key = $key;
        $this->value = $value;
        $this->expiresAfter = $expiresAfter;
    }

    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function isHit()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function set($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function expiresAt($expiration)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function expiresAfter($time)
    {
        return $this->expiresAfter;
    }
}