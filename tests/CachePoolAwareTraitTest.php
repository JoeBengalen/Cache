<?php

namespace JoeBengalen\Cache\Test;

class CachePoolAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    public function setUp()
    {
        $this->pool = $this->getMock('\Psr\Cache\CacheItemPoolInterface');
        $this->object = new CachePoolAwareObject();
    }

    public function testSetCachePoolInObject()
    {
        $this->assertInstanceOf('\JoeBengalen\Cache\Test\CachePoolAwareObject', $this->object->setCachePool($this->pool));
    }

    public function testGetCachePoolFromObject()
    {
        $this->object->setCachePool($this->pool);
        $this->assertSame($this->pool, $this->object->getCachePool());
    }

    public function testCheckIfObjectHasCachePool()
    {
        $this->assertFalse($this->object->hasCachePool());
        $this->object->setCachePool($this->pool);
        $this->assertTrue($this->object->hasCachePool());
    }
}
