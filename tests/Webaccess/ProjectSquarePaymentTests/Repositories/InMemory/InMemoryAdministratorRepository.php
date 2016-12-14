<?php

namespace Webaccess\ProjectSquarePaymentTests\Repositories\InMemory;

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

    public function getByID($administratorID)
    {
        return (isset($this->objects[$administratorID])) ? clone $this->objects[$administratorID] : false;
    }

    public function persist(Administrator $administrator)
    {
        if (!$administrator->getID()) {
            $administrator->setId($this->getNextID());
        }
        $this->objects[$administrator->getID()] = $administrator;

        return true;
    }
}