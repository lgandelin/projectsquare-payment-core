<?php

namespace Webaccess\ProjectSquarePayment\Repositories\InMemory;

use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;

class InMemoryPlatformRepository implements PlatformRepository
{
    public $objects;

    public function persist(Platform $platform): bool
    {
        $platformID = $platform->getID() ? $platform->getID() : $this->getNextID();
        $platform->setId($platformID);
        $this->objects[$platformID] = $platform;

        return true;
    }

    public function getByID($platformID): Platform
    {
        return $this->objects[$platformID];
    }

    private function getNextID()
    {
        return sizeof($this->objects) + 1;
    }
}