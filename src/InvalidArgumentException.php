<?php

namespace JoeBengalen\Cache;

use Psr\Cache\InvalidArgumentException as InvalidArgumentExceptionInterface;

class InvalidArgumentException extends \InvalidArgumentException implements InvalidArgumentExceptionInterface
{
}
