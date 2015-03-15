<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Repository\RepositoryInterface;
use JoeBengalen\Cache\Item;
use Psr\Cache\CacheItemInterface;

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
    
    // return:boolean
    
    /**
     * Cache item.
     * 
     * @param \JoeBengalen\Cache\Item $item Item to cache.
     * 
     * @return boolean True
     */
    public function store(Item $item)
    {
        $this->data[$item->getKey()] = $item;
        
        return true;
    }
    
    // Item[] list of items
    
    /**
     * Cache multiple items.
     * 
     * @param array $items
     * @return boolean
     */
    public function storeAll(array $items)
    {
        foreach ($items as $item) {
            $this->store($item);
        }
        
        return true;
    }
    
    // keys:string
    public function delete($key)
    {
        unset($this->data[$key]);
        
        return true;
    }
    
    // keys:string[]
    public function deleteAll(array $keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        
        return true;
    }
    
    // return:boolean
    public function clear()
    {
        $this->data = [];
        
        return true;
    }
}


