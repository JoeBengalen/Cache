<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Item;

interface SimpleRepositoryInterface
{
    // key: string, return:boolean
    public function contains($key);
        
    // key: string, return: Item|null
    public function fetch($key);
        
    // return: boolean
    public function store(Item $item);
    
    // key: string, return: boolean
    public function delete($key);
    
    // return: boolean
    public function clear();
}