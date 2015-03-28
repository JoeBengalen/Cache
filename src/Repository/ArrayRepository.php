<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Repository\SimpleRepositoryInterface;
use JoeBengalen\Cache\Item;

class ArrayRepository implements SimpleRepositoryInterface
{
    /**
     * @var \JoeBengalen\Cache\Item[] $data Cached items.
     */
    protected $data = [];
    
    public function contains($key)
    {
        return isset($this->data[$key]);
    }
    
    public function fetch($key)
    {
        return $this->contains($key) ? clone $this->data[$key] : null;
    }
    
    public function store(Item $item)
    {
        $this->data[$item->getKey()] = $item;
        
        return true;
    }
    
    public function delete($key)
    {
        unset($this->data[$key]);
        
        return true;
    }
    
    public function clear()
    {
        $this->data = [];
        
        return true;
    }
}
