<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Repository\SimpleRepositoryInterface;

interface RepositoryInterface extends SimpleRepositoryInterface
{
    /**
     * Check for each given key if the cache repository contains an item with that key.
     * 
     * @param string[] $keys Indexed array of keys
     * 
     * @return array Associative array with booleans linked to the keys. 
     *               Boolean true if an item if found with that key, false if not.
     */
    public function containsAll(array $keys);
    
    /**
     * Fetch multiple items from the cache repository.
     * 
     * @param string[] $keys Indexed array of keys.
     * 
     * @return array Associative array with \JoeBengalen\Cache\Item or null linked to 
     *               each key. Null if no item was found with that key.
     */
    public function fetchAll(array $keys);
    
    /**
     * Store multiple items into the cache repostiroy.
     * 
     * If storing an item fails, it will continue trying to store the other
     * items. If any store failed, false will be returned.
     * 
     * @param string[] $items Indexed array of keys.
     * 
     * @return boolean True on succes, false on failure.
     */
    public function storeAll(array $items);
    
    /**
     * Delete multiple items from the cache repository.
     * 
     * If the deletion of an item fails, it will continue trying to delete the other
     * items. If any deletion failed, false will be returned.
     * 
     * @param string[] $keys Indexed array of keys.
     * 
     * @return boolean True on succes, false on failure.
     */
    public function deleteAll(array $keys);
}
