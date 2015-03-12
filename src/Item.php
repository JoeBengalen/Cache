<?php

namespace JoeBengalen\Cache;

use Psr\Cache\CacheItemInterface;
use JoeBengalen\Cache\InvalidArgumentException;
use DateTime;

class Item implements CacheItemInterface
{
    /**
     * @var string $hit 
     */
    protected $key;
    
    /**
     * @var mixed $value 
     */
    protected $value;
    
    /**
     * @var \DateTime|null $expiration 
     */
    protected $expiration;
    
    /**
     * @var boolean $hit 
     */
    protected $hit;
    
    public function __construct($key, $ttl = null, $hit = false)
    {
        if (!$this->validKey($key)) {
            throw new InvalidArgumentException("Invalid key");
        }
        
        $this->key = $key;
        $this->setExpiration($ttl);
        $this->setHit($hit);
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * @return boolean True if the request resulted in a cache hit, false otherwise.
     */
    public function isHit()
    {
        return $this->hit;
    }

    /**
     * Confirms if the cache item exists in the cache.
     *
     * @return boolean True if item exists in the cache, false otherwise.
     */
    public function exists()
    {
        return $this->isHit();
    }

    /**
     * Retrieves the value of the item from the cache associated with this objects key.
     *
     * @return mixed Value corresponding to this cache item's key, or null if no hit.
     */
    public function get()
    {
        return $this->isHit() ? $this->value : null;
    }
    
    /**
     * Returns the key for the current cache item.
     *
     * @return string Key string for this cache item.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Returns the expiration time of a not-yet-expired cache item.
     *
     * If this cache item is a Cache Miss, this method MAY return the time at
     * which the item expired or the current time if that is not available.
     *
     * @return \DateTime|null Timestamp at which this cache item will expire, null means it will not expire.
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP.
     *
     * @param mixed $value Serializable Value to be stored.
     * 
     * @return static Invoked object.
     */
    public function set($value)
    {
        $this->value = $value;
        
        return $this;
    }

    /**
     * Sets the expiration for this cache item.
     *
     * @param int|\DateTime $ttl (optional)
     *   - If an integer is passed, it is interpreted as the number of seconds
     *     after which the item MUST be considered expired.
     *   - If a DateTime object is passed, it is interpreted as the point in
     *     time after which the item MUST be considered expired.
     *   - If null is passed, a default value MAY be used. If none is set,
     *     the value should be stored permanently or for as long as the
     *     implementation allows.
     *
     * @return static Invoked object.
     */
    public function setExpiration($ttl = null)
    {
        if (is_null($ttl)) {
            $this->expiration = null;
        } elseif ($ttl instanceof DateTime) {
            $this->expiration = $ttl;
        } elseif (is_int($ttl)) {
            $this->expiration = new DateTime("+{$ttl} seconds");
        } else {
            throw new InvalidArgumentException(printf("ttl must either be null, \DateTime or an integer. A %s was given."), [gettype($ttl)]);
        }
        
        return $this;
    }
    
    /**
     * Set if there was a hit or not
     * 
     * @param boolean $hit
     * 
     * @return static Invoked object.
     * 
     * @throws \JoeBengalen\Cache\InvalidArgumentException If $hit was not a boolean.
     */
    public function setHit($hit)
    {
        if (!is_bool($hit)) {
            throw new InvalidArgumentException(printf("hit must be boolean. A %s was given.", [gettype($hit)]));
        }
        
        $this->hit = $hit;
        
        return $this;
    }
    
    /**
     * Check if the key is valid.
     * 
     * @param string $key Key to validate.
     * 
     * @return boolean True is key is valid, false otherwise.
     */
    protected function validKey($key)
    {
        return is_string($key);
    }
}