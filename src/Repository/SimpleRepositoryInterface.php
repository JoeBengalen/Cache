<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Item;

interface SimpleRepositoryInterface
{
    /**
     * Check if the cache repository contains an item with the given key.
     * 
     * @param string $key Key of the item to check
     * 
     * @return boolean True if an item is found with the key, false if not.
     */
    public function contains($key);
        
    /**
     * Fetch an item from the cache repository by its key.
     * 
     * @param string $key Key of the item to fetch.
     * 
     * @return \JoeBengalen\Cache\Item|null Item if one is found with the key, null if not.
     */
    public function fetch($key);
        
    /**
     * Store an item into the cache repository.
     * 
     * @param \JoeBengalen\Cache\Item $item Item to store.
     * 
     * @return boolean True on succes, false on failure.
     */
    public function store(Item $item);
    
    /**
     * Delete an item from the cache repository.
     * 
     * @param string $key Key of the item to delete.
     * 
     * @return boolean True on succes, false on failure.
     */
    public function delete($key);
    
    /**
     * Remove all items from the repository.
     * 
     * @return boolean True on succes, false on failure.
     */
    public function clear();
}
