<?php

namespace Chief\Decorators;

use Chief\Cache;
use Chief\CacheableCommand;
use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;
use Chief\HasCacheOptions;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachingDecoratorTest extends DecoratorTest
{
    /** @var ObjectProphecy */
    private $cache;

    public function test_can_be_instantiated_and_implements_decorator()
    {
        $decorator = $this->getDecorator();
        $decorator->setInnerBus($this->prophesize(CommandBus::class)->reveal());
        $this->assertInstanceOf(CommandBus::class, $decorator);
        $this->assertInstanceOf(Decorator::class, $decorator);
    }

    public function test_commands_not_implementing_cacheable_are_ignored()
    {
        $decorator = $this->getDecorator();
        $inner = $this->prophesize(CommandBus::class);
        $decorator->setInnerBus($inner->reveal());

        $command = $this->prophesize(Command::class)->reveal();

        $this->cache->save(Argument::any())->shouldNotBeCalled();
        $this->cache->getItem(Argument::any())->shouldNotBeCalled();

        $inner->execute($command)->shouldBeCalled()->willReturn(7);

        $result = $decorator->execute($command);
        $this->assertEquals(7, $result);
    }

    public function test_execution_return_value_is_cached_when_command_implements_cacheable()
    {
        $decorator = $this->getDecorator();
        $inner = $this->prophesize(CommandBus::class);
        $decorator->setInnerBus($inner->reveal());

        $command = new FakeCachableCommand('fizzbuzz');

        $notCachedItem = $this->prophesize(CacheItemInterface::class);
        $notCachedItem->isHit()->willReturn(false);
        $cacheKey = md5(serialize(($command)));
        $notCachedItem->getKey()->willReturn($cacheKey);
        $this->cache->getItem($cacheKey)->willReturn($notCachedItem->reveal());

        $notCachedItem->set(7)->shouldBeCalled()->willReturn($notCachedItem->reveal());
        $notCachedItem->expiresAfter(3600)->shouldBeCalled()->willReturn($notCachedItem->reveal());

        $this->cache->save(Argument::that(function (CacheItemInterface $item) use ($command) {
            return $item->getKey() === md5(serialize($command));
        }))->shouldBeCalled();

        $inner->execute($command)->shouldBeCalled()->willReturn(7);

        $result = $decorator->execute($command);
        $this->assertEquals(7, $result);
    }

    public function test_cache_expiry_can_be_overridden()
    {
        $decorator = $this->getDecorator();
        $inner = $this->prophesize(CommandBus::class);
        $decorator->setInnerBus($inner->reveal());

        $command = new FakeCachableCommandWithCacheOptions('fizzbuzz', 60*60*24*365, null);

        $notCachedItem = $this->prophesize(CacheItemInterface::class);
        $notCachedItem->isHit()->willReturn(false);
        $notCachedItem->getKey()->willReturn(md5(serialize(($command))));
        $this->cache->getItem(Argument::any())->willReturn($notCachedItem->reveal());

        $notCachedItem->set(7)->shouldBeCalled()->willReturn($notCachedItem->reveal());
        $notCachedItem->expiresAfter(60*60*24*365)->shouldBeCalled()->willReturn($notCachedItem->reveal());

        $this->cache->save(Argument::that(function (CacheItemInterface $item) use ($command) {
            return !empty($item->getKey());
        }))->shouldBeCalled();

        $inner->execute($command)->shouldBeCalled()->willReturn(7);

        $result = $decorator->execute($command);
        $this->assertEquals(7, $result);
    }

    public function test_cache_key_can_be_overridden()
    {
        $decorator = $this->getDecorator();
        $inner = $this->prophesize(CommandBus::class);
        $decorator->setInnerBus($inner->reveal());

        $command = new FakeCachableCommandWithCacheOptions('fizzbuzz', null, 'custom-cache-key');

        $notCachedItem = $this->prophesize(CacheItemInterface::class);
        $notCachedItem->isHit()->willReturn(false);
        $notCachedItem->getKey()->willReturn('custom-cache-key');
        $this->cache->getItem('custom-cache-key')->willReturn($notCachedItem->reveal());

        $notCachedItem->set(7)->shouldBeCalled()->willReturn($notCachedItem->reveal());
        $notCachedItem->expiresAfter(3600)->shouldBeCalled()->willReturn($notCachedItem->reveal());

        $this->cache->save(Argument::that(function (CacheItemInterface $item) use ($command) {
            return $item->getKey() === 'custom-cache-key';
        }))->shouldBeCalled();

        $inner->execute($command)->shouldBeCalled()->willReturn(7);

        $result = $decorator->execute($command);
        $this->assertEquals(7, $result);
    }

    public function test_cache_item_value_is_returned_if_cached()
    {
        $decorator = $this->getDecorator();
        $inner = $this->prophesize(CommandBus::class);
        $decorator->setInnerBus($inner->reveal());

        $command = new FakeCachableCommand('fizzbuzz');

        $cachedItem = $this->prophesize(CacheItemInterface::class);
        $cachedItem->isHit()->willReturn(true);
        $cachedItem->get()->willReturn('foo');
        $this->cache->getItem(Argument::any())->willReturn($cachedItem->reveal());
        $this->cache->save(Argument::any())->shouldNotBeCalled();

        $inner->execute($command)->shouldNotBeCalled();
        $result = $decorator->execute($command);
        $this->assertEquals('foo', $result);
    }

    /**
     * @return \Chief\Decorator
     */
    protected function getDecorator()
    {
        $this->cache = $this->prophesize(CacheItemPoolInterface::class);
        return new CachingDecorator($this->cache->reveal());
    }
}

class FakeCachableCommand implements CacheableCommand
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}

class FakeCachableCommandWithCacheOptions extends FakeCachableCommand implements HasCacheOptions
{
    private $expiry;
    private $cacheKey;

    public function __construct($data, $expiry, $cacheKey)
    {
        parent::__construct($data);
        $this->expiry = $expiry;
        $this->cacheKey = $cacheKey;
    }

    public function getCacheExpiry()
    {
        return $this->expiry;
    }

    public function getCacheKey()
    {
        return $this->cacheKey;
    }
}
