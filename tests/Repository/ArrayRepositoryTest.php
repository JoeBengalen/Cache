<?php

namespace JoeBengalen\Cache\Test\Repository;

use JoeBengalen\Cache\Repository\ArrayRepository;

class ArrayRepositoryTest extends AbstractSimpleRepositoryTest
{
    public function setUp()
    {
        $this->repository = new ArrayRepository();
    }
}
