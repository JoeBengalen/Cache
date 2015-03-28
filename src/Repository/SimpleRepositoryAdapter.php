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
    
    /**
     * Check if cache repository contains cache for key.
     * 
     * @param string $key Key to check
     * 
     * @return boolean True if cache is found for key, false otherwise.
     */
    public function contains($key)
    {
        return $this->repository->contains($key);
    }
    
    /**
     * Fetch cached item.
     * 
     * @param string $key Key of the item to fetch.
     * 
     * @return \JoeBengalen\Cache\Item|null Cache item if found for key, null otherwise.
     */
    public function fetch($key)
    {
        return $this->repository->fetch($key);
    }
    
    /**
     * Cache item.
     * 
     * @param \JoeBengalen\Cache\Item $item Item to cache.
     * 
     * @return boolean True on succes, false on failure.
     */
    public function store(Item $item)
    {
        return $this->repository->store($item);
    }
    
    /**
     * Delete cached item.
     * 
     * @param string $key Key of the item to delete.
     * 
     * @return boolean True on succes, false on failure.
     */
    public function delete($key)
    {
        return $this->repository->delete($key);
    }
    
    /**
     * Clear all cached data.
     * 
     * @return boolean True on succes, false on failure.
     */
    public function clear()
    {
        return $this->repository->clear();
    }
    
    /**
     * Check if the repository contains cache for each key.
     * 
     * @param string[] $keys Indexed array of keys
     * 
     * @return array Associative array with booleans linked to the keys. Boolean true if cache if found, false otherwise.
     */
    public function containsAll(array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->repository->contains($key);
        }
        return $result;
    }
    
    /**
     * Fetch multiple cached items.
     * 
     * @param string[] $keys Indexed array of keys.
     * 
     * @return array Associative array with \JoeBengalen\Cache\Item or null linked to the keys. Null if no cached item was found for key.
     */
    public function fetchAll(array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->repository->fetch($key);
        }
        return $result;
    }
    
    /**
     * Cache multiple items.
     * 
     * If storing of an item fails, it will continue trying to store the other
     * items. If any store failed, false will be returned.
     * 
     * @param string[] $items Indexed array of keys.
     * 
     * @return boolean True on succes, false on failure.
     */
    public function storeAll(array $items)
    {
        $result = true;
        foreach ($items as $item) {
            $result = $this->repository->store($item) ? $result : false;
        }
        return $result;
    }
    
    /**
     * Delete multiple cached items.
     * 
     * If the deletion of a key fails, it will continue trying to delete the other
     * keys. If any deletion failed, false will be returned.
     * 
     * @param string[] $keys Indexed array or keys.
     * 
     * @return boolean True on succes, false on failure.
     */
    public function deleteAll(array $keys)
    {
        $result = true;
        foreach ($keys as $key) {
            $result = $this->repository->delete($key) ? $result : false;
        }
        return $result;
    }
}
