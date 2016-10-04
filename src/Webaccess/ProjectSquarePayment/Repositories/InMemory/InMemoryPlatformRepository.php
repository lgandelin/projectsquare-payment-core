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
        return (isset($this->objects[$platformID])) ? clone $this->objects[$platformID] : false;
    }

    public function getBySlug($platformSlug)
    {
        foreach ($this->objects as $platform) {
            if ($platform->getSlug() == $platformSlug) {
                return clone $platform;
            }
        }

        return false;
    }

    public function getAll()
    {
        return $this->objects;
    }

    public function persist(Platform $platform)
    {
        if (!$platform->getID()) {
            $platform->setId($this->getNextID());
        }
        $this->objects[$platform->getID()] = $platform;

        return $platform->getID();
    }

    public function deleteByID($platformID)
    {
        if (isset($this->objects[$platformID])) {
            unset($this->objects[$platformID]);
        }
    }
}