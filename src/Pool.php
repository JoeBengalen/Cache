<?php

namespace JoeBengalen\Cache;

use JoeBengalen\Cache\Item;
use JoeBengalen\Cache\Repository\RepositoryInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheException;

class Pool implements CacheItemPoolInterface
{
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
        
        // perform fetch, not first a contains as this may result in multiple fetch calls
        $item = $this->repository->fetch($key);
        
        if ($item instanceof Item) {
            return $item->setHit(true);
        }
        
        return new Item($key, $this->defaultTtl);
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys           Indexed array of keys of items to retrieve.
     * 
     * @return array|\Traversable   Traversable collection of Cache Items keyed by the cache keys of
     *                              each item. A Cache item will be returned for each key, even if that
     *                              key is not found. However, if no keys are specified then an empty
     *                              traversable MUST be returned instead.
     */
    public function getItems(array $keys = [])
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }
        
        return $items;
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
    public function save(Item $item)
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
        $result = $this->repository->persist(array_values($this->deferred));
        $this->deferred = [];
        
        return $result;
    }
    
    public function __destruct()
    {
        $this->commit();
    }
}