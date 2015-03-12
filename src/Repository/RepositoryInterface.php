<?php

namespace JoeBengalen\Cache\Repository;

use Psr\Cache\CacheItemInterface;

// api: contains, fetch, persist, delete and clear. And xxAll for multiple items at once, except for the latter.
interface RepositoryInterface
{
    // key: string, return:boolean
    public function contains($key);
    
    // key: string[], return: array as [key => boolean]
    public function containsAll(array $keys);
    
    // key: string, return: Item|null
    public function fetch($key);
    
    // key: string[], return: array as [key => Item|null]
    public function fetchAll(array $keys);
    
    // return: boolean
    public function persist(CacheItemInterface $item);
    
    // item: Item[], return: boolean
    public function persistAll(array $items);
    
    // key: string, return: boolean
    public function delete($key);
    
    // keys: string[], return: boolean
    public function deleteAll(array $keys);
    
    // return: boolean
    public function clear();
}