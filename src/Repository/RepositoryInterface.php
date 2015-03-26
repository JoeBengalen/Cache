<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Item;

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
    public function store(Item $item);
    
    // item: Item[], return: boolean
    public function storeAll(array $items);
    
    // key: string, return: boolean
    public function delete($key);
    
    // keys: string[], return: boolean
    public function deleteAll(array $keys);
    
    // return: boolean
    public function clear();
}