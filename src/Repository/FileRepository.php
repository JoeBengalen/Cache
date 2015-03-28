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

    public function contains($key)
    {
        return !is_null($this->findFilename($key));
    }
    
    public function fetch($key)
    {
        if (!$this->contains($key)) {
            return;
        }
        
        return unserialize(file_get_contents($this->findFilename($key)));
    }
    
    public function store(Item $item)
    {
        $this->delete($item->getKey());
        return file_put_contents($this->generateNewFilename($item), serialize($item)) !== false;
    }
    
    public function delete($key)
    {
        foreach ($this->findFilenameList($key) as $filename) {
            unlink($filename);
        }
        
        return true;
    }
    
    public function clear()
    {
        foreach ($this->findFilenameList('*') as $filename) {
            unlink($filename);
        }
        
        return true;
    }
}
