<?php

namespace JoeBengalen\Cache\Repository;

interface RepositoryInterface
{
    public function fetch($key); // key:string, return:Item|null
    public function clear(); // return:boolean
    public function persist($keys); // keys:string|string[]
    public function delete($keys); // keys:string|string[]
}

