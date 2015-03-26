<?php

namespace JoeBengalen\Cache;

use Psr\Cache\CacheItemInterface;
use JoeBengalen\Cache\InvalidArgumentException;
use DateTime;
use DateTimeImmutable;
use DateInterval;

/**
 * Cache pool item.
 */
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
     * @var integer|null $defaultTtl Default time to life of an newly set value, null is infinite.
     */
    protected $defaultTtl;

    /**
     * @var boolean $cached Indicates if the item was saved in cache or not. 
     */
    protected $cached = false;

    /**
     * Create cache item
     * 
     * @param string $key Unique cache identifier
     * @param integer|null $defaultTtl (optional) Default time to life of an newly set value, null is infinite.
     * 
     * @throws \JoeBengalen\Cache\InvalidArgumentException If $defaultTtl is not an integer or null.
     */
    public function __construct($key, $defaultTtl = null)
    {
        if (!$this->validKey($key)) {
            throw new InvalidArgumentException("Invalid key");
        }
        
        if (!is_null($defaultTtl) && !is_integer($defaultTtl)) {
            throw new InvalidArgumentException(printf("DefaultTtl must be of type integer or null, %s given.", gettype($defaultTtl)));
        }
        
        $this->key        = $key;
        $this->defaultTtl = $defaultTtl;
    }
    
    /**
     * Check if the item is expired.
     * 
     * @return boolean True if item is expired, false otherwise.
     */
    public function isExpired()
    {
        return $this->getExpiration() <= new \DateTime('now');
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * @return boolean True if the request resulted in a cache hit, false otherwise.
     */
    public function isHit()
    {
        return $this->exists() && !$this->isExpired();
    }

    /**
     * Confirms if the cache item exists in the cache.
     *
     * @return boolean True if item exists in the cache, false otherwise.
     */
    public function exists()
    {
        return $this->cached;
    }

    /**
     * Retrieves the value of the item from the cache associated with this objects key.
     *
     * @return mixed|null Value corresponding to this cache item's key, or null if no hit.
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
     * If the cache item never expires a \DateTime of 1 year in the future 
     * will be returned, as the return value MUST be a \DateTime object.
     *
     * @return \DateTime Timestamp at which this cache item will expire.
     */
    public function getExpiration()
    {
        return !is_null($this->expiration) ? $this->expiration : new DateTime('+ 1 year');
    }

    /**
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP.
     *
     * @param mixed $value Serializable Value to be stored.
     * @param int|\DateTime $ttl
     *   - If an integer is passed, it is interpreted as the number of seconds
     *     after which the item MUST be considered expired.
     *   - If a DateTime object is passed, it is interpreted as the point in
     *     time after which the item MUST be considered expired.
     *   - If no value is passed, a default value MAY be used. If none is set,
     *     the value should be stored permanently or for as long as the
     *     implementation allows.
     * 
     * @return static Invoked object.
     */
    public function set($value, $ttl = null)
    {
        if (is_null($ttl)) {
            $ttl = $this->defaultTtl;
        }
        
        if (is_null($ttl)) {
            $this->expiration = null; // TODO: Awkward situation, as this mst NOT be returned by getExpiration(). Fault in psr-6 spec!
        } elseif (is_integer($ttl)) {
            $this->expiresAfter($ttl);
        } elseif ($ttl instanceof DateTime) {
            $this->expiresAt($ttl);
        } else {
            throw new InvalidArgumentException(printf("Ttl must be of type \DateTime, integer or null, %s given.", gettype($ttl)));
        }
        
        $this->value = $value;
        
        return $this;
    }

    /**
     * Sets the expiration time this cache item.
     *
     * @param \DateTime|\DateTimeImmutable $expiration
     *   The point in time after which the item MUST be considered expired.
     *   If null is passed explicitly, a default value MAY be used. If none is set,
     *   the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static Called object.
     * 
     * @throws \JoeBengalen\Cache\InvalidArgumentException If $expiration is not of type \DataTime or \DateTimeImmutable
     */
    public function expiresAt($expiration)
    {
        if ($expiration instanceof DateTime) {
            $this->expiration = $expiration;
        } elseif ($expiration instanceof DateTimeImmutable) {            
            $this->expiration = new DateTime();
            $this->expiration->setTimestamp($expiration->getTimestamp());
        } else {
            throw new InvalidArgumentException(printf("Expiration must be of type \DateTime or \DateTimeImmutable, %s given.", gettype($expiration)));
        }
        
        return $this;
    }

    /**
     * Sets the expiration time this cache item.
     *
     * @param int|\DateInterval $time
     *   The period of time from the present after which the item MUST be considered
     *   expired. An integer parameter is understood to be the time in seconds until
     *   expiration.
     *
     * @return static Called object.
     */
    public function expiresAfter($time)
    {
        if (is_integer($time)) {
            $this->expiration = new DateTime("{$time} seconds");
        } elseif ($time instanceof DateInterval) {
            $now = new \DateTime('now');
            $this->expiration = $now->add($time);
        } else {
            throw new InvalidArgumentException(printf("Time must be of type integer or \DateInterval, %s given.", gettype($time)));
        }
        
        return $this;
    }

    /**
     * Mark the item as cached
     * 
     * @return static Invoked object.
     */
    public function markCached()
    {
        $this->cached = true;

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
        // TODO: Look at this, 
        //       looks like there should be one less backslah, 
        //       but for some reason PHP givens an error!                    
        //return is_string($key) && preg_match("/[(){}\/\\]/", $key) === 0; 
        return is_string($key) && preg_match("/[(){}\/\\\]/", $key) === 0;
    }
}