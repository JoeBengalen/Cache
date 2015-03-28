<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Repository\SimpleRepositoryInterface;
use JoeBengalen\Cache\Repository\RepositoryInterface;
use JoeBengalen\Cache\Item;

class SimpleRepositoryAdapter implements RepositoryInterface
{
    /**
     * @var \JoeBengalen\Cache\Repository\SimpleRepositoryInterface $repository Adapted object.
     */
    protected $repository;
    
    /**
     * Map a SimpleRepositoryInterface to a RepositoryInterface.
     * 
     * @param \JoeBengalen\Cache\Repository\SimpleRepositoryInterface $repository Object to wrap.
     */
    public function __construct(SimpleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    public function contains($key)
    {
        return $this->repository->contains($key);
    }
    
    public function fetch($key)
    {
        return $this->repository->fetch($key);
    }
    
    public function store(Item $item)
    {
        return $this->repository->store($item);
    }
    
    public function delete($key)
    {
        return $this->repository->delete($key);
    }
    
    public function clear()
    {
        return $this->repository->clear();
    }
    
    public function containsList(array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->repository->contains($key);
        }
        return $result;
    }
    
    public function fetchList(array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->repository->fetch($key);
        }
        return $result;
    }
    
    public function storeList(array $items)
    {
        $result = true;
        foreach ($items as $item) {
            $result = $this->repository->store($item) ? $result : false;
        }
        return $result;
    }
    
    public function deleteList(array $keys)
    {
        $result = true;
        foreach ($keys as $key) {
            $result = $this->repository->delete($key) ? $result : false;
        }
        return $result;
    }
}
