<?php

namespace JoeBengalen\Cache\Test\Repository;

use JoeBengalen\Cache\Repository\SessionRepository;
use JoeBengalen\Cache\Test\Repository\AbstractSimpleRepositoryTest;
use JoeBengalen\Cache\Test\Repository\InactiveSessionRepository;

class SessionRepositoryTest extends AbstractSimpleRepositoryTest
{
    public function setUp()
    {
        $this->repository = new SessionRepository();
    }
    
    public function testCheckIfSessionIsActive()
    {
        $this->assertTrue($this->repository->isSessionActive());
    }
    
    public function testThrowsExceptionIfSessionNotActive()
    {
        $this->setExpectedException('\JoeBengalen\Cache\CacheException');
        new InactiveSessionRepository();
    }
}
