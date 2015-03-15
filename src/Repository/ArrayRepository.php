<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Repository\RepositoryInterface;
use JoeBengalen\Cache\Item;

class ArrayRepository implements RepositoryInterface
{
    /**
     * @var \JoeBengalen\Cache\Item[] Cached items.
     */
    protected $data = [];
        
    /**
     * Check if cache repository contains cache for key.
     * 
     * @param string $key Key to check
     * 
     * @return boolean True if cache is found for key, false otherwise.
     */
    public function contains($key)
    {
        return isset($this->data[$key]);
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
            $result[$key] = $this->contains($key);
        }
        
        return $result;
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
        return $this->contains($key) ? $this->data[$key] : null;
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
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->fetch($key);
        }
        
        return $items;
    }
    
    /**
     * Cache item.
     * 
     * @param \JoeBengalen\Cache\Item $item Item to cache.
     * 
     * @return boolean True.
     */
    public function store(Item $item)
    {
        $this->data[$item->getKey()] = $item;
        
        return true;
    }
    
    /**
     * Cache multiple items.
     * 
     * @param string[] $items Indexed array of keys.
     * 
     * @return boolean True.
     */
    public function storeAll(array $items)
    {
        foreach ($items as $item) {
            $this->store($item);
        }
        
        return true;
    }
    
    /**
     * Delete cached item.
     * 
     * @param string $key Key of the item to delete.
     * 
     * @return boolean True.
     */
    public function delete($key)
    {
        unset($this->data[$key]);
        
        return true;
    }
    
    /**
     * Delete multiple cached items.
     * 
     * @param string[] $keys Indexed array or keys.
     * 
     * @return boolean True.
     */
    public function deleteAll(array $keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        
        return true;
    }
    
    /**
     * Clear all cached data.
     * 
     * @return boolean True.
     */
    public function clear()
    {
        $this->data = [];
        
        return true;
    }
}


