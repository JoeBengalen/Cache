<?php

namespace JoeBengalen\Cache\Test\Repository;

use JoeBengalen\Cache\Repository\SessionRepository;

class InactiveSessionRepository extends SessionRepository
{
    public function isSessionActive()
    {
        return false;
    }
}
