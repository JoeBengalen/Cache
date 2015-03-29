<?php

namespace JoeBengalen\Cache;

use JoeBengalen\Cache\Repository\RepositoryInterface;
use JoeBengalen\Cache\Repository\SimpleRepositoryAdapter;
use JoeBengalen\Cache\Repository\SimpleRepositoryInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Cache pool.
 */
class Pool implements CacheItemPoolInterface
{
    /**
     * @var \JoeBengalen\Cache\Repository\RepositoryInterface
     */
    protected $repository;

    /**
     * @var int|null Default time to live of an item in seconds. Null means it does not expire.
     */
    protected $defaultTtl;

    /**
     * @var \JoeBengalen\Cache\Item[] Deferred items.
     */
    protected $deferred = [];

    /**
     * Create cache pool.
     *
     * @param \JoeBengalen\Cache\Repository\RepositoryInterface|
     *        \JoeBengalen\Cache\Repository\SimpleRepositoryInterface
     *                      $repository Resository to store the items in.
     * @param int|null $defaultTtl Default time to live of an item in seconds.
     *                             Null means it does not expire.
     *
     * @throws \JoeBengalen\Cache\InvalidArgumentException If the $repository is not \JoeBengalen\Cache\Repository\RepositoryInterface
     *                                                     or \JoeBengalen\Cache\Repository\SimpleRepositoryInterface.
     * @throws \JoeBengalen\Cache\InvalidArgumentException If the $defaultTtl is not integer or null.
     */
    public function __construct($repository, $defaultTtl = 3600)
    {
        if ($repository instanceof SimpleRepositoryInterface && !$repository instanceof RepositoryInterface) {
            $repository = new SimpleRepositoryAdapter($repository);
        }

        if (!$repository instanceof RepositoryInterface) {
            throw new InvalidArgumentException(printf("Repository must be \JoeBengalen\Cache\Repository\RepositoryInterface or \JoeBengalen\Cache\Repository\SimpleRepositoryInterface, %s given", gettype($repository)));
        }

        if (!is_null($defaultTtl) && !is_integer($defaultTtl)) {
            throw new InvalidArgumentException(printf('DefaultTtl must be integer, %s given.', gettype($defaultTtl)));
        }

        $this->repository = $repository;
        $this->defaultTtl = $defaultTtl;
    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * @param string $key Key for which to return the corresponding Cache Item.
     *
     * @throws \JoeBengalen\Cache\InvalidArgumentException If the $key is an illegal key
     *
     * @return \JoeBengalen\Cache\Item Corresponding Cache Item.
     */
    public function getItem($key)
    {
        if (isset($this->deferred[$key])) {
            return $this->deferred[$key];
        }

        if ($this->repository->contains($key)) {
            return $this->repository->fetch($key);
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
     * @throws \JoeBengalen\Cache\InvalidArgumentException If $keys contains an illegal key.
     *
     * @return \JoeBengalen\Cache\Item[] List of Cache Items keyed by the cache keys of each item.
     *                                   If no keys are specified then an empty array will be returned.
     */
    public function getItems(array $keys = [])
    {
        // Method uses a complecated logic to make sure only the containsList and fetchList
        // repository function are called, as that may improve performance over multiple
        // contains and fetch calls.

        $containsResult = $this->repository->containsList($keys);
        $cachedKeys     = array_keys(array_filter($containsResult));
        $cachedItems    = $this->repository->fetchList($cachedKeys);
        $uncachedItems  = array_filter($containsResult, function ($contains) {
            return !$contains;
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
     * @return bool True if the pool was successfully cleared. False if there was an error.
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
     * @return self.
     */
    public function deleteItems(array $keys)
    {
        $this->repository->deleteList($keys);

        return $this;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param \JoeBengalen\Cache\Item $item Cache item to save.
     *
     * @throws \JoeBengalen\Cache\InvalidArgumentException If $item is not \JoeBengalen\Cache\Item.
     *
     * @return self.
     */
    public function save(CacheItemInterface $item)
    {
        if (!$item instanceof Item) {
            throw new InvalidArgumentException(printf("Item must be \JoeBengalen\Cache\Item, %s given.", gettype($item)));
        }

        $item->markCached();
        $this->repository->store($item);

        return $this;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param \JoeBengalen\Cache\Item $item Cache item to save.
     *
     * @throws \JoeBengalen\Cache\InvalidArgumentException If $item is not \JoeBengalen\Cache\Item.
     *
     * @return self.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        if (!$item instanceof Item) {
            throw new InvalidArgumentException(printf("Item must be \JoeBengalen\Cache\Item, %s given.", gettype($item)));
        }

        $this->deferred[$item->getKey()] = $item;

        return $this;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool TRUE if all not-yet-saved items were successfully saved. FALSE otherwise.
     */
    public function commit()
    {
        $items = array_values($this->deferred);

        /* @var \JoeBengalen\Cache\Item $item */
        array_walk($items, function (&$item) {
            $item->markCached();
        });

        $result = $this->repository->storeList($items);
        $this->deferred = [];

        return $result;
    }

    /**
     * Destruct object.
     *
     * Make sure all deferred items are saved.
     */
    public function __destruct()
    {
        $this->commit();
    }

    /**
     * Get a Item instance.
     *
     * @param string $key Key of item object to create.
     *
     * @throws \JoeBengalen\Cache\InvalidArgumentException If the $key is an illegal key
     *
     * @return \JoeBengalen\Cache\Item Created item object.
     */
    protected function createItem($key)
    {
        return new Item($key, $this->defaultTtl);
    }
}
