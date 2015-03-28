<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Repository\SimpleRepositoryInterface;
use JoeBengalen\Cache\Item;

interface RepositoryInterface extends SimpleRepositoryInterface
{
    // key: string[], return: array as [key => boolean]
    public function containsAll(array $keys);
    
    // key: string[], return: array as [key => Item|null]
    public function fetchAll(array $keys);
    
    // item: Item[], return: boolean
    public function storeAll(array $items);
    
    // keys: string[], return: boolean
    public function deleteAll(array $keys);
}
