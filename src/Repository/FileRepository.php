<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\InvalidArgumentException;
use JoeBengalen\Cache\Item;

class FileRepository implements SimpleRepositoryInterface
{
    /**
     * @var string Existing writable directory to store files.
     */
    protected $directory;

    /**
     * @var string Extenstion for files.
     */
    protected $extension;

    /**
     * Create a file repository.
     *
     * @param string $directory Existing writable directory.
     * @param string $extension (optional) File extention for files.
     *
     * @throws \JoeBengalen\Cache\InvalidArgumentException If $directory is not a valid directory.
     * @throws \JoeBengalen\Cache\InvalidArgumentException If $extension is not a string.
     * @throws \JoeBengalen\Cache\InvalidArgumentException If $directory is not writable.
     */
    public function __construct($directory, $extension = 'cache')
    {
        if (!is_string($directory) || !is_dir($directory)) {
            throw new InvalidArgumentException('Directory must be an existing directory.');
        }
        if (!is_string($extension)) {
            throw new InvalidArgumentException(printf('Extension must be string, %s given.', gettype($extension)));
        }

        if (!is_writable($directory)) {
            throw new InvalidArgumentException(printf('Directory %s must be writable.', $directory));
        }

        $this->directory = realpath($directory).DIRECTORY_SEPARATOR;
        $this->extension = '.'.str_replace(['*'], [''], $extension); // when allowing wildcard here users could delete the entire directory content by deleting key '*'
    }

    /**
     * Generate full file path bases on filename.
     *
     * @param string $filename Filename to create the full path for.
     *
     * @return string Generated file path.
     */
    public function generateFilepath($filename)
    {
        return $this->directory.$filename.$this->extension;
    }

    /**
     * Get a list of files based on a filename.
     *
     * @param type $filename Filename to find files for.
     *
     * @return string[] List of filenames.
     */
    public function findFiles($filename)
    {
        return glob($this->generateFilepath("*.$filename"));
    }

    /**
     * Get the first found filename based on an items key.
     *
     * @param string $filename Item key to find the file for.
     *
     * @return string|null Filename or null of not found.
     */
    public function findFile($filename)
    {
        $list = $this->findFiles($filename);

        return !empty($list) ? $list[0] : null;
    }

    /**
     * Generage filename for item.
     *
     * @param Item $item Item to generate a filename for.
     *
     * @return string Generated filename.
     */
    public function generateFilename($item)
    {
        return $item->getExpiration()->format('YmdHis').'.'.$item->getKey();
    }

    /**
     * {@inheritdoc}
     */
    public function contains($key)
    {
        return !is_null($this->findFile($key));
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        if (!$this->contains($key)) {
            return;
        }

        return unserialize(file_get_contents($this->findFile($key)));
    }

    /**
     * {@inheritdoc}
     */
    public function store(Item $item)
    {
        $this->delete($item->getKey());

        return file_put_contents($this->generateFilepath($this->generateFilename($item)), serialize($item)) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        foreach ($this->findFiles($key) as $filename) {
            unlink($filename);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        foreach ($this->findFiles('*') as $filename) {
            unlink($filename);
        }

        return true;
    }
}
