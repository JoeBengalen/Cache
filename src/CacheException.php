<?php

namespace JoeBengalen\Cache;

use Psr\Cache\CacheException as CacheExceptionInterface;

class CacheException extends \Exception implements CacheExceptionInterface
{
}