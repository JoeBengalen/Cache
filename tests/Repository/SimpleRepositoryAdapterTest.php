<?php

namespace JoeBengalen\Cache\Test\Repository;

use JoeBengalen\Cache\Repository\SimpleRepositoryWrapper;

class SimpleRepositoryWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->repository = new ArrayRepository();
    }
}
