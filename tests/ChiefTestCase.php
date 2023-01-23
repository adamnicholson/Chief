<?php

namespace Chief;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

abstract class ChiefTestCase extends TestCase
{
    use ProphecyTrait;
}