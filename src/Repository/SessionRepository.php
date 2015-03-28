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
     * @var array $data Reference to an index of the global variable $_SESSION.
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
    
    public function contains($key)
    {
        return isset($this->data[$key]);
    }
    
    public function fetch($key)
    {
        return $this->contains($key) ? clone $this->data[$key] : null;
    }
    
    public function store(Item $item)
    {
        $this->data[$item->getKey()] = $item;
        
        return true;
    }
    
    public function delete($key)
    {
        unset($this->data[$key]);
        
        return true;
    }
    
    public function clear()
    {
        $this->data = [];
        
        return true;
    }
}
