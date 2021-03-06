<?php
/**
 * JoeBengalen Cache library.
 *
 * @author      Martijn Wennink <joebengalen@gmail.com>
 * @copyright   Copyright (c) 2015 Martijn Wennink
 * @license     https://github.com/JoeBengalen/Cache/blob/master/LICENSE.md (MIT License)
 *
 * @version     0.1.0
 */

namespace JoeBengalen\Cache;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Cache pool aware trait.
 * 
 * Implements the control of a cache pool to an object.
 */
trait CachePoolAwareTrait
{
    /**
     * @var \Psr\Cache\CacheItemPoolInterface $cachePool 
     */
    protected $cachePool;
    
    /**
     * Set a cache pool.
     * 
     * @param \Psr\Cache\CacheItemPoolInterface $cachePool Cache pool to set.
     * 
     * @return static Invoked object.
     */
    public function setCachePool(CacheItemPoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;
        
        return $this;
    }
    
    /**
     * Get the current set cache pool.
     * 
     * @return \Psr\Cache\CacheItemPoolInterface|null Curent cache pool or null of none is set.
     */
    public function getCachePool()
    {
        return $this->cachePool;
    }
    
    /**
     * Check if a cache pool is set.
     * 
     * @return boolean True is a cahce pool set, false otherwise.
     */
    public function hasCachePool()
    {
        return $this->cachePool instanceof CacheItemPoolInterface;
    }
}
