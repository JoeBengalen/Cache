<?php

namespace JoeBengalen\Cache\Test\Repository;

use JoeBengalen\Cache\Repository\ArrayRepository;
use JoeBengalen\Cache\Test\Repository\AbstractSimpleRepositoryTest;

class ArrayRepositoryTest extends AbstractSimpleRepositoryTest
{
    public function setUp()
    {
        $this->repository = new ArrayRepository();
    }
}
