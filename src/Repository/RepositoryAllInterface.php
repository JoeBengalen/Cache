<?php

namespace JoeBengalen\Cache\Repository;

interface RepositoryAllInterface extends RepositoryInterface
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