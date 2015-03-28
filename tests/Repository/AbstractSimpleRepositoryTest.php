<?php

namespace JoeBengalen\Cache\Test\Repository;

abstract class AbstractSimpleRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected $repository;
    
    public function createItem($key = 'default.key')
    {
        return $this->getMock('\JoeBengalen\Cache\Item', null, [$key]);
    }
    
    public function testStoreItemInRepository()
    {
        $this->assertTrue($this->repository->store($this->createItem('key.store')));
        $this->assertTrue($this->repository->contains('key.store'));
    }
    
    public function testContainsItemInRepository()
    {
        $this->assertTrue($this->repository->store($this->createItem('key1')));
        $this->assertTrue($this->repository->contains('key1'));
        $this->assertFalse($this->repository->contains('key2'));
    }
    
    public function testFetchItemFromRepository()
    {
        $item = $this->createItem('key.fetch');
        $this->assertTrue($this->repository->store($item));
        $this->assertEquals($item, $this->repository->fetch('key.fetch'));
        $this->assertNull($this->repository->fetch('key.null'));
    }
    
    public function testDeleteItemFromRepository()
    {
        $this->assertTrue($this->repository->store($this->createItem('key.delete')));
        $this->assertTrue($this->repository->contains('key.delete'));
        $this->assertTrue($this->repository->delete('key.delete'));
        $this->assertFalse($this->repository->contains('key.delete'));
    }
    
    public function testClearRepository()
    {
        $this->assertTrue($this->repository->store($this->createItem('key1')));
        $this->assertTrue($this->repository->store($this->createItem('key2')));
        $this->assertTrue($this->repository->contains('key1'));
        $this->assertTrue($this->repository->contains('key2'));
        $this->assertTrue($this->repository->clear());
        $this->assertFalse($this->repository->contains('key1'));
        $this->assertFalse($this->repository->contains('key2'));
    }
}
