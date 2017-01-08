<?php

namespace Chief\Decorator\Cache;

use Chief\Decorator\Cache\CacheableCommand;

interface HasCacheOptions extends CacheableCommand
{
    /**
     * @return int|null
     *  In how many seconds from now should this cache item expire. Return null to use the default value specified
     *  in the CachingDecorator.
     */
    public function getCacheExpiry();

    /**
     * @return string|null
     *  The cache key used when caching this object. Return null to automatically generate a cache key.
     */
    public function getCacheKey();
}
