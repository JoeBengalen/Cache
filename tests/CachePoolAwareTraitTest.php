<?php

namespace JoeBengalen\Cache\Test;

use JoeBengalen\Cache\CachePoolAwareTrait;

class CachePoolAwareTraitObject
{
    use CachePoolAwareTrait;
}

class CachePoolAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->pool = $this->getMock('\Psr\Cache\CacheItemPoolInterface');
        $this->obj = new CachePoolAwareTraitObject();
    }
    
    public function testSetGetCachePool()
    {
        $this->assertInstanceOf('\JoeBengalen\Cache\Test\CachePoolAwareTraitObject', $this->obj->setCachePool($this->pool));
        $this->assertSame($this->pool, $this->obj->getCachePool());
    }
    
    public function testHasCachePool()
    {
        $this->assertFalse($this->obj->hasCachePool());
        $this->obj->setCachePool($this->pool);
        $this->assertTrue($this->obj->hasCachePool());
    }
}