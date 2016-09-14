<?php

namespace Webaccess\ProjectSquarePayment\Repositories\InMemory;

use Webaccess\ProjectSquarePayment\Entities\Administrator;
use Webaccess\ProjectSquarePayment\Repositories\AdministratorRepository;

class InMemoryAdministratorRepository implements AdministratorRepository
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

    public function getByID($administratorID): Administrator
    {
        return $this->objects[$administratorID];
    }

    public function persist(Administrator $administrator): bool
    {
        if (!$administrator->getID()) {
            $administrator->setId($this->getNextID());
        }
        $this->objects[$administrator->getID()] = $administrator;

        return true;
    }
}