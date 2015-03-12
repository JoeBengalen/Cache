<?php

namespace JoeBengalen\Cache;

use JoeBengalen\Cache\Item;
use JoeBengalen\Cache\Repository\RepositoryInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheException;

class Pool implements CacheItemPoolInterface
{
    /**
     * @var \JoeBengalen\Cache\Repository\RepositoryInterface $repository 
     */
    protected $repository;
    
    protected $defaultTtl = 3600; // 1 hour
    
    protected $deferred = [];


    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Returns a Cache Item representing the specified key.
     *
     * @param string $key Key for which to return the corresponding Cache Item.
     * 
     * @return \JoeBengalen\Cache\Item Corresponding Cache Item.
     * 
     * @throws \JoeBengalen\Cache\InvalidArgumentException If the $key is not a legal value
     */
    public function getItem($key)
    {
        if (isset($this->deferred[$key])) {
            return $this->deferred[$key];
        }
        
        if ($this->repository->contains($key)) {
            $item = $this->repository->fetch($key);
            $item->setHit(true);
            return $item;
        }
        
        return $this->createItem($key);
    }

    /**
     * Returns a list of cache items.
     * 
     * A Cache item will be returned for each key, even if that key is not found. 
     *
     * @param array $keys Indexed array of keys of items to retrieve.
     * 
     * @return \JoeBengalen\Cache\Item[] List of Cache Items keyed by the cache keys of each item.
     *                                   If no keys are specified then an empty array will be returned.
     */
    public function getItems(array $keys = [])
    {
        // Method uses a complecated logic to make sure only the containsAll and fetchAll 
        // repository function are called, as that may improve performance over multiple 
        // contains and fetch calls.
        
        $containsResult = $this->repository->containsAll($keys);
        $cachedKeys     = array_keys(array_filter($containsResult));
        $cachedItems    = $this->repository->fetchAll($cachedKeys);
        $uncachedItems  = array_filter($containsResult, function ($contains) {
            return !$contains;
        });
        
        // Set hit to true on each cached item.
        array_walk($cachedItems, function (&$item) {
            $item->setHit(true);
        });
        
        // Create new item object for each uncached item.
        array_walk($uncachedItems, function (&$value, $key) {
            $value = $this->createItem($key);
        });
        
        // Merge the cached and uncached items.
        // NOTE: Use the keys as base to make sure the order 
        //       of the array stays the same as the input keys.
        return array_merge(
            array_flip($keys),
            $cachedItems,
            $uncachedItems
        );
    }

    /**
     * Deletes all items in the pool.
     *
     * @return boolean True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        return $this->repository->clear();
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param array $keys Array of keys that should be removed from the pool.
     * 
     * @return static Invoked object.
     */
    public function deleteItems(array $keys)
    {
        $this->repository->delete($keys);
        return $this;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param \JoeBengalen\Cache\Item $item Cache item to save.
     *
     * @return static Invoked object.
     */
    public function save(CacheItemInterface $item)
    {
        $this->repository->persist($item);
        return $this;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param \JoeBengalen\Cache\Item $item Cache item to save.
     *
     * @return static Invoked object.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferred[$item->getKey()] = $item;
        
        return $this;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return boolean TRUE if all not-yet-saved items were successfully saved. FALSE otherwise.
     */
    public function commit()
    {
        $result = $this->repository->persistAll(array_values($this->deferred));
        $this->deferred = [];
        
        return $result;
    }
    
    public function __destruct()
    {
        $this->commit();
    }
    
    protected function createItem($key)
    {
        return new Item($key, $this->defaultTtl);
    }
}