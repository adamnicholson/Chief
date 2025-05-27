<?php

namespace Chief\Tests\Bridge\Psr;

use Chief\Bridge\Psr\PsrContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class PsrContainerTest extends TestCase
{
    public function testMakeReturnsObjectFromPsrContainer()
    {
        $mock = $this->createMock(ContainerInterface::class);
        $expected = new \stdClass();
        $mock->expects($this->once())
            ->method('get')
            ->with('SomeClass')
            ->willReturn($expected);

        $container = new PsrContainer($mock);
        $result = $container->make('SomeClass');
        $this->assertSame($expected, $result);
    }
}
