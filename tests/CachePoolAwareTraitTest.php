<?php

namespace JoeBengalen\Cache\Test;

use JoeBengalen\Cache\CachePoolAwareTrait;

class CachePoolAwareTraitObject
{
    use CachePoolAwareTrait;
}

class CachePoolAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    protected $object;
    
    public function setUp()
    {
        $this->pool = $this->getMock('\Psr\Cache\CacheItemPoolInterface');
        $this->object = new CachePoolAwareTraitObject();
    }
    
    public function testSetCachePoolInObject()
    {
        $this->assertInstanceOf('\JoeBengalen\Cache\Test\CachePoolAwareTraitObject', $this->object->setCachePool($this->pool));
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