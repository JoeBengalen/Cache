<?php

namespace JoeBengalen\Cache\Test\Repository;

use JoeBengalen\Cache\Repository\SimpleRepositoryAdapter;

class SimpleRepositoryAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $mock = $this->getMock('\JoeBenaglen\Cache\Repository\SimpleRepositoryInterface');
        $this->repository = new SimpleRepositoryAdapter($mock);
    }
}
