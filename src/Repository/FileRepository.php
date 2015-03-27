<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Repository\SimpleRepositoryInterface;
use JoeBengalen\Cache\InvalidArgumentException;
use JoeBengalen\Cache\Item;

/**
 * TODO: Update all docblocks!
 */
class FileRepository implements SimpleRepositoryInterface
{
    protected $directory;
    protected $extension;
    
    public function __construct($directory, $extension = 'cache')
    {
        if (!is_string($directory) || !is_dir($directory)) {
            throw new InvalidArgumentException("Directory must be an existing directory.");
        }
        if (!is_string($extension)) {
            throw new InvalidArgumentException(printf("Extension must be string, %s given.", gettype($extension)));
        }
        
        if (!is_writable($directory)) {
            throw new InvalidArgumentException(printf('Directory %s must be writable.', $directory));
        }
        
        $this->directory = realpath($directory) . DIRECTORY_SEPARATOR;
        $this->extension = '.' . str_replace(['*'], [''], $extension); // when allowing wildcard here users could delete the entire directory content by deleting key '*'
    }
    
    public function generateFilename($name)
    {
        return $this->directory . $name . $this->extension;
    }
    
    public function findFilenameList($key)
    {
        $filename = $this->generateFilename("*.$key");
        return glob($filename);
    }
    
    public function findFilename($key)
    {
        $list = $this->findFilenameList($key);
        return !empty($list) ? $list[0] : null;
    }
    
    public function generateNewFilename(Item $item)
    {
        return $this->generateFilename($item->getExpiration()->format("YmdHis") . '.' . $item->getKey());
    }

    /**
     * Check if cache repository contains cache for key.
     * 
     * @param string $key Key to check
     * 
     * @return boolean True if cache is found for key, false otherwise.
     */
    public function contains($key)
    {
        return !is_null($this->findFilename($key));
    }
        
    /**
     * Fetch cached item.
     * 
     * The cached item is cloned to break the reference.
     * 
     * @param string $key Key of the item to fetch.
     * 
     * @return \JoeBengalen\Cache\Item|null Cache item if found for key, null otherwise.
     */
    public function fetch($key)
    {
        if (!$this->contains($key)) {
            return;
        }
        
        return unserialize(file_get_contents($this->findFilename($key)));
    }
    
    /**
     * Cache item.
     * 
     * @param \JoeBengalen\Cache\Item $item Item to cache.
     * 
     * @return boolean True.
     */
    public function store(Item $item)
    {
        $this->delete($item->getKey());
        return file_put_contents($this->generateNewFilename($item), serialize($item)) !== false;
    }
    
    /**
     * Delete cached item.
     * 
     * @param string $key Key of the item to delete.
     * 
     * @return boolean True.
     */
    public function delete($key)
    {
        foreach ($this->findFilenameList($key) as $filename) {
            unlink($filename);
        }
        
        return true;
    }
    
    /**
     * Clear all cached data.
     * 
     * @return boolean True.
     */
    public function clear()
    {
        foreach ($this->findFilenameList('*') as $filename) {
            unlink($filename);
        }
        
        return true;
    }
}
