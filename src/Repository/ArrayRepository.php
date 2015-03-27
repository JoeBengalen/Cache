<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Repository\SimpleRepositoryInterface;
use JoeBengalen\Cache\Item;

class ArrayRepository implements SimpleRepositoryInterface
{
    /**
     * @var \JoeBengalen\Cache\Item[] $data Cached items.
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
     * Fetch cached item.
     * 
     * The cached item is cloned to break the reference.
     * 
     * @param string $key Key of the item to fetch.
     * 
     * @return \JoeBengalen\Cache\Item|null Cache item if found for key, null otherwise.
     */
    public function fetch($key)
    {
        return $this->contains($key) ? clone $this->data[$key] : null;
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
