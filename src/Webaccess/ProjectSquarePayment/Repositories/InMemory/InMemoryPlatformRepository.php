<?php

namespace Webaccess\ProjectSquarePayment\Repositories\InMemory;

use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;

class InMemoryPlatformRepository implements PlatformRepository
{
    public $objects;

    public function __construct()
    {
        $this->objects = [];
    }

    private function getNextID()
    {
        return sizeof($this->objects) + 1;
    }

    public function getByID($platformID)
    {
        return (isset($this->objects[$platformID])) ? $this->objects[$platformID] : false;
    }

    public function getBySlug($platformSlug)
    {
        foreach ($this->objects as $platform) {
            if ($platform->getSlug() == $platformSlug) {
                return $platform;
            }
        }

        return false;
    }

    public function persist(Platform $platform): bool
    {
        $platformID = $platform->getID() ? $platform->getID() : $this->getNextID();
        $platform->setId($platformID);
        $this->objects[$platformID] = $platform;

        return true;
    }
}