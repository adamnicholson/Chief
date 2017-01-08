<?php

namespace Chief\Decorator\Cache;

use Chief\SynchronousCommandBus;
use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachingDecorator implements Decorator
{
    /**
     * @var CommandBus
     */
    private $innerBus;
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;
    /**
     * @var int
     */
    private $expiresAfter;

    /**
     * CachingDecorator constructor.
     * @param CacheItemPoolInterface $cache
     * @param int $expiresAfter
     * @param CommandBus $innerCommandBus
     */
    public function __construct(CacheItemPoolInterface $cache, $expiresAfter = 3600, CommandBus $innerCommandBus = null)
    {
        $this->cache = $cache;
        $this->expiresAfter = $expiresAfter;
        $this->setInnerBus($innerCommandBus ?: new SynchronousCommandBus);
    }

    /**
     * @inheritdoc
     */
    public function setInnerBus(CommandBus $bus)
    {
        $this->innerBus = $bus;
    }

    /**
     * @inheritdoc
     */
    public function execute(Command $command)
    {
        if (!$command instanceof CacheableCommand) {
            return $this->innerBus->execute($command);
        }

        $cached = $this->cache->getItem($this->getCacheKey($command));
        if ($cached->isHit()) {
            return $cached->get();
        }

        $value = $this->innerBus->execute($command);

        $this->cache->save($this->createCacheItem($command, $value));

        return $value;
    }

    /**
     * Create a new cache item to be persisted.
     *
     * @param CacheableCommand $command
     * @param mixed $value
     * @return CacheItemInterface
     */
    private function createCacheItem(CacheableCommand $command, $value)
    {
        return $this->cache->getItem($this->getCacheKey($command))
            ->expiresAfter($this->getCacheExpiry($command))
            ->set($value);
    }

    /**
     * Create the key to be used when saving this item to the cache pool.
     *
     * The cache item key is taken as a the (string) serialized command, to ensure the return value is unique
     * depending on the command properties; that serialized string is then md5'd to ensure it doesn't
     * overflow any string length limits the implementing CacheItemPoolInterface library has.
     *
     * @param CacheableCommand $command
     * @return string
     */
    private function getCacheKey(CacheableCommand $command)
    {
        if ($command instanceof HasCacheOptions && $command->getCacheKey()) {
            return $command->getCacheKey();
        }

        return md5(serialize($command));
    }

    /**
     * Determine when this CachableCommand should expire, in terms of seconds from now.
     *
     * @param CacheableCommand $command
     * @return int
     */
    private function getCacheExpiry(CacheableCommand $command)
    {
        if ($command instanceof HasCacheOptions && $command->getCacheExpiry() > 0) {
            return $command->getCacheExpiry();
        }

        return $this->expiresAfter;
    }
}
