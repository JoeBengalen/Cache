<?php

namespace JoeBengalen\Cache\Repository;

use JoeBengalen\Cache\Item;

class ArrayRepository implements SimpleRepositoryInterface
{
    /**
     * @var \JoeBengalen\Cache\Item[] Cached items.
     */
    protected $data = [];

    /**
     * {@inheritdoc}
     */
    public function contains($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        return $this->contains($key) ? clone $this->data[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function store(Item $item)
    {
        $this->data[$item->getKey()] = $item;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        unset($this->data[$key]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->data = [];

        return true;
    }
}
