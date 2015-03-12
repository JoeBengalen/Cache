<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Repository\RepositoryInterface;
use Psr\Cache\CacheItemInterface;

class ArrayRepository implements RepositoryInterface
{
    protected $data = [];
    
    // key: string, return:boolean
    public function contains($key)
    {
        return isset($this->data[$key]);
    }
    
    // key: string[], return: array as [key => boolean]
    public function containsAll(array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->contains($key);
        }
        
        return $result;
    }
    
    // key:string, return:Item|null
    public function fetch($key)
    {
        return $this->contains($key) ? $this->data[$key] : null;
    }
    
    // key:string[], return:array as [key => Item|null]
    public function fetchAll(array $keys)
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->fetch($key);
        }
        
        return $items;
    }
    
    // return:boolean
    public function persist(CacheItemInterface $item)
    {
        $this->data[$item->getKey()] = $item;
        
        return true;
    }
    
    // Item[] list of items
    public function persistAll(array $items)
    {
        foreach ($items as $item) {
            $this->persist($item);
        }
        
        return true;
    }
    
    // keys:string
    public function delete($key)
    {
        unset($this->data[$key]);
        
        return true;
    }
    
    // keys:string[]
    public function deleteAll(array $keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        
        return true;
    }
    
    // return:boolean
    public function clear()
    {
        $this->data = [];
        
        return true;
    }
}


