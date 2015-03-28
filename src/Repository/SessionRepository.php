<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Repository\SimpleRepositoryInterface;
use JoeBengalen\Cache\Item;
use JoeBengalen\Cache\CacheException;

/**
 * TODO: Update all docblocks!
 */
class SessionRepository implements SimpleRepositoryInterface
{
    /**
     * @var array $data Reference to $_SESSION value.
     */
    protected $data;
    
    /**
     * Create session repository.
     * 
     * @param string $sessionKey Session key to use.
     * 
     * @throws \JoeBengalen\Cache\CacheException If PHP session is not active.
     */
    public function __construct($sessionKey = 'joebengalen.cache.pool')
    {
        if (!$this->isSessionActive()) {
            throw new CacheException('PHP session must be active.');
        }
        
        $this->data = &$_SESSION[$sessionKey];
    }
    
    /**
     * Check if PHP session is active.
     * 
     * @return boolean True is a session is active, false otherwise.
     */
    public function isSessionActive()
    {
        return session_status() === PHP_SESSION_ACTIVE;
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
