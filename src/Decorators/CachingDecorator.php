<?php

namespace Chief\Decorators;

use Chief\CacheableCommand;
use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;
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
     */
    public function __construct(CacheItemPoolInterface $cache, $expiresAfter = 3600)
    {
        $this->cache = $cache;
        $this->expiresAfter = $expiresAfter;
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

        $cached = $this->cache->getItem(self::createCacheKey($command));
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
     * @return CacheItem
     */
    private function createCacheItem(CacheableCommand $command, $value)
    {
        return new CacheItem(
            $this->createCacheKey($command),
            $value,
            $this->expiresAfter
        );
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
    private function createCacheKey(CacheableCommand $command)
    {
        return md5(serialize($command));
    }
}
