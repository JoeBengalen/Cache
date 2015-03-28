<?php

namespace JoeBengalen\Cache\Test;

use JoeBengalen\Cache\Item;
use DateTime;
use DateTimeImmutable;
use DateInterval;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function itemFactory($value)
    {
        $item = new Item('key1');
        $item->set($value, 10);
        return $item->markCached();
    }
    
    public function invalidKeyProvider()
    {
        return [
            ['t{est'], 
            ['t}est'], 
            ['t(est'], 
            ['t)est'], 
            ['test/'], 
            ['\\test'],
        ];
    }
    
    public function testGetItemKey()
    {
        $item = new Item('key');
        $this->assertEquals('key', $item->getKey());
    }
    
    /**
     * @dataProvider invalidKeyProvider
     */
    public function testItemThrowsExceptionOnInvalidKey($invalidKey)
    {
        $this->setExpectedException('\JoeBengalen\Cache\InvalidArgumentException');
        new Item($invalidKey);
    }
    
    public function testItemWithoutExpiration()
    {
        $item = new Item('test1', null);
        $item->set(null);
        $expected = new DateTime('+ 1 year');
        $this->assertEquals($expected, $item->getExpiration());
    }
    
    public function testItemWithDefaultTimeToLive()
    {
        $item = new Item('test1', 3600);
        $item->set(null);
        $expected = new DateTime('+ 3600 seconds');
        $this->assertEquals($expected, $item->getExpiration());
    }
    
    public function testItemThrowsExceptionOnInvalidDefaultTimeToLive()
    {
        $this->setExpectedException('\JoeBengalen\Cache\InvalidArgumentException');
        new Item('key', 'invalid');
    }
    
    public function testItemExpiresAtGivenDateTime()
    {
        $item = new Item('key');
        $this->assertInstanceOf('\JoeBengalen\Cache\Item', $item->expiresAt(new DateTime('10 seconds')));
        $this->assertEquals(new DateTime('10 seconds'), $item->getExpiration());
    }
    
    public function testItemExpiresAtGivenDateTimeImmutable()
    {
        if (version_compare(phpversion(), '5.5.0', '>=')) {
            $item = new Item('key');
            $this->assertInstanceOf('\JoeBengalen\Cache\Item', $item->expiresAt(new DateTimeImmutable('10 seconds')));
            $this->assertEquals(new DateTime('10 seconds'), $item->getExpiration());
        }
    }
    
    public function testItemThrowsExceptionOnInvalidExpiresAt()
    {
        $item = new Item('key');
        $this->setExpectedException('\JoeBengalen\Cache\InvalidArgumentException');
        $item->expiresAt('invalid');
    }
    
    public function testItemExpiresAfterGivenSeconds()
    {
        $item = new Item('key');
        
        $this->assertInstanceOf('\JoeBengalen\Cache\Item', $item->expiresAfter(10));
        $this->assertEquals(new DateTime('10 seconds'), $item->getExpiration());
        $this->assertFalse($item->isExpired());
        
        $this->assertInstanceOf('\JoeBengalen\Cache\Item', $item->expiresAfter(-10));
        $this->assertEquals(new DateTime('-10 seconds'), $item->getExpiration());
        $this->assertTrue($item->isExpired());
    }
    
    public function testItemExpiresAfterGivenDateInterval()
    {
        $item = new Item('key');
        $this->assertInstanceOf('\JoeBengalen\Cache\Item', $item->expiresAfter(new DateInterval('PT10S')));
        $this->assertEquals(new DateTime('10 seconds'), $item->getExpiration());
        $this->assertFalse($item->isExpired());
    }
    
    public function testItemThrowsExceptionOnInvalidExpiresAfter()
    {
        $item = new Item('key');
        $this->setExpectedException('\JoeBengalen\Cache\InvalidArgumentException');
        $item->expiresAfter('invalid');
    }
    
    public function testSetItemValueWithSpecifiedTimeToLive()
    {
        $item = new Item('test1');
        $item->set(null, 10);
        $future = new DateTime('+ 10 seconds');
        $this->assertEquals($future, $item->getExpiration());
    }
    
    public function testSetValueWithSpecifiedExpiration()
    {
        $item = new Item('test1');
        $expected = new DateTime('+ 10 seconds');
        $item->set(null, $expected);
        $this->assertEquals($expected, $item->getExpiration());
    }
    
    public function testItemThrowsExceptionOnInvalidTimeToLiveWhenSettingValue()
    {
        $this->setExpectedException('\JoeBengalen\Cache\InvalidArgumentException');
        $item = new Item('key');
        $item->set(null, 'invalid');
    }
    
    public function testItemExistsAfterMarkedAsCached()
    {
        $item = new Item('key');
        $this->assertFalse($item->exists());
        $this->assertInstanceOf('\JoeBengalen\Cache\Item', $item->markCached());
        $this->assertTrue($item->exists());
    }
    
    public function testItemIsExpired()
    {
        $item = new Item('key');
        $item->expiresAfter(10);
        $this->assertFalse($item->isExpired());
        $item->expiresAfter(-10);
        $this->assertTrue($item->isExpired());
    }
    
    public function testItemIsHit()
    {
        $item = $this->itemFactory(null);
        $item->expiresAfter(-10);
        $this->assertTrue($item->exists());
        $this->assertTrue($item->isExpired());
        $this->assertFalse($item->isHit());
        $item->expiresAfter(10);
        $this->assertTrue($item->isHit());
    }
    
    public function testSettingItemValueOfNull()
    {
        $item = $this->itemFactory(null);
        $this->assertNull($item->get());
    }
    
    public function testSettingItemValueOfTypeBoolean()
    {
        $item = $this->itemFactory(true);
        $this->assertTrue($item->get());
        
        $item = $this->itemFactory(false);
        $this->assertFalse($item->get());
    }
    
    public function testSettingItemValueOfTypeInteger()
    {
        $item = $this->itemFactory(33);
        $this->assertSame(33, $item->get());
    }
    
    public function testSettingItemValueOfTypeFloat()
    {
        $item = $this->itemFactory(1.234);
        $this->assertSame(1.234, $item->get());
    }
    
    public function testSettingItemValueOfTypeString()
    {
        $item = $this->itemFactory('string value');
        $this->assertSame('string value', $item->get());
    }
    
    public function testSettingItemValueOfTypeObject()
    {
        $obj = new \stdClass();
        $obj->test = true;
        $item = $this->itemFactory($obj);
        $this->assertSame($obj, $item->get());
    }
}