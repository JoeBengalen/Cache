<?php

namespace JoeBengalen\Cache\Test\Repository;

use JoeBengalen\Cache\Repository\SimpleRepositoryAdapter;

class SimpleRepositoryAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $repository;

    public function createItem($key = 'default.key')
    {
        return $this->getMock('\JoeBengalen\Cache\Item', null, [$key]);
    }

    public function createSimpleRepository()
    {
        return $this->getMock('\JoeBengalen\Cache\Repository\SimpleRepositoryInterface');
    }

    public function setUpSimpleRepositoryAdapter($simpleRepository = null)
    {
        $mock = $simpleRepository ? $simpleRepository : $this->createSimpleRepository();

        $this->repository = new SimpleRepositoryAdapter($mock);
    }

    public function testAdaptedRepositoryContainsItem()
    {
        $mock = $this->createSimpleRepository();
        $mock
            ->expects($this->exactly(2))
            ->method('contains')
            ->with($this->logicalOr(
                 $this->equalTo('key1'),
                 $this->equalTo('key2')
             ))
            ->will($this->returnCallback(function ($key) {
                return $key === 'key1';
            }));
        $this->setUpSimpleRepositoryAdapter($mock);

        $this->assertTrue($this->repository->contains('key1'));
        $this->assertFalse($this->repository->contains('key2'));
    }

    public function testFetchItemFromAdaptedRepository()
    {
        $mock = $this->createSimpleRepository();
        $mock
            ->expects($this->exactly(2))
            ->method('fetch')
            ->with($this->logicalOr(
                 $this->equalTo('key1'),
                 $this->equalTo('key2')
             ))
            ->will($this->returnCallback(function ($key) {
                return $key === 'key1' ? $this->createItem($key) : null;
            }));
        $this->setUpSimpleRepositoryAdapter($mock);

        $this->assertInstanceOf('\JoeBengalen\Cache\Item', $this->repository->fetch('key1'));
        $this->assertNull($this->repository->fetch('key2'));
    }

    public function testStoreItemIntoAdaptedRepository()
    {
        $mock = $this->createSimpleRepository();
        $mock
            ->expects($this->exactly(1))
            ->method('store')
            ->with($this->isInstanceOf('\JoeBengalen\Cache\Item'))
            ->willReturn(true);
        $this->setUpSimpleRepositoryAdapter($mock);

        $this->assertTrue($this->repository->store($this->createItem('key1')));
    }

    public function testDeleteItemFromAdaptedRepository()
    {
        $mock = $this->createSimpleRepository();
        $mock
            ->expects($this->exactly(1))
            ->method('delete')
            ->with($this->equalTo('key1'))
            ->willReturn(true);
        $this->setUpSimpleRepositoryAdapter($mock);

        $this->assertTrue($this->repository->delete('key1'));
    }

    public function testClearAdaptedRepository()
    {
        $mock = $this->createSimpleRepository();
        $mock
            ->expects($this->exactly(1))
            ->method('clear')
            ->willReturn(true);
        $this->setUpSimpleRepositoryAdapter($mock);

        $this->assertTrue($this->repository->clear());
    }

    public function testAdaptedRepositoryContainsItemList()
    {
        $mock = $this->createSimpleRepository();
        $mock
            ->expects($this->exactly(2))
            ->method('contains')
            ->with($this->logicalOr(
                 $this->equalTo('key1'),
                 $this->equalTo('key2')
             ))
            ->will($this->returnCallback(function ($key) {
                return $key === 'key1';
            }));
        $this->setUpSimpleRepositoryAdapter($mock);

        $result = $this->repository->containsList(['key1', 'key2']);

        $this->assertCount(2, $result);
        $this->assertArrayHasKey('key1', $result);
        $this->assertArrayHasKey('key2', $result);
        $this->assertTrue($result['key1']);
        $this->assertFalse($result['key2']);
    }

    public function testFetchItemListFromAdaptedRepository()
    {
        $mock = $this->createSimpleRepository();
        $mock
            ->expects($this->exactly(2))
            ->method('fetch')
            ->with($this->logicalOr(
                 $this->equalTo('key1'),
                 $this->equalTo('key2')
             ))
            ->will($this->returnCallback(function ($key) {
                return $key === 'key1' ? $this->createItem($key) : null;
            }));
        $this->setUpSimpleRepositoryAdapter($mock);

        $result = $this->repository->fetchList(['key1', 'key2']);

        $this->assertCount(2, $result);
        $this->assertArrayHasKey('key1', $result);
        $this->assertArrayHasKey('key2', $result);
        $this->assertInstanceOf('\JoeBengalen\Cache\Item', $result['key1']);
        $this->assertNull($result['key2']);
    }

    public function testStoreItemListIntoAdaptedRepository()
    {
        $mock = $this->createSimpleRepository();
        $mock
            ->expects($this->exactly(3))
            ->method('store')
            ->with($this->isInstanceOf('\JoeBengalen\Cache\Item'))
            ->willReturn(true);
        $this->setUpSimpleRepositoryAdapter($mock);

        $this->assertTrue($this->repository->storeList([
            $this->createItem('key1'),
            $this->createItem('key2'),
            $this->createItem('key3'),
        ]));
    }

    public function testStoreItemListIntoAdaptedRepositoryReturnsFalseIfAnyFailed()
    {
        $counter = 0;
        $mock = $this->createSimpleRepository();
        $mock
            ->expects($this->exactly(3))
            ->method('store')
            ->with($this->isInstanceOf('\JoeBengalen\Cache\Item'))
            ->will($this->returnCallback(function () use (&$counter) {
                return ++$counter !== 2; // return false on second call
            }));
        $this->setUpSimpleRepositoryAdapter($mock);

        $this->assertFalse($this->repository->storeList([
            $this->createItem('key1'),
            $this->createItem('key2'),
            $this->createItem('key3'),
        ]));
    }

    public function testDeleteItemListFromAdaptedRepository()
    {
        $mock = $this->createSimpleRepository();
        $mock
            ->expects($this->exactly(2))
            ->method('delete')
            ->with($this->logicalOr(
                 $this->equalTo('key1'),
                 $this->equalTo('key2')
             ))
            ->willReturn(true);
        $this->setUpSimpleRepositoryAdapter($mock);

        $this->assertTrue($this->repository->deleteList(['key1', 'key2']));
    }

    public function testDeleteItemListFromAdaptedRepositoryReturnsFalseIfAnyFailed()
    {
        $counter = 0;
        $mock = $this->createSimpleRepository();
        $mock
            ->expects($this->exactly(3))
            ->method('delete')
            ->with($this->logicalOr(
                 $this->equalTo('key1'),
                 $this->equalTo('key2'),
                 $this->equalTo('key3')
             ))
            ->will($this->returnCallback(function () use (&$counter) {
                return ++$counter !== 2; // return false on second call
            }));
        $this->setUpSimpleRepositoryAdapter($mock);

        $this->assertFalse($this->repository->deleteList(['key1', 'key2', 'key3']));
    }
}
