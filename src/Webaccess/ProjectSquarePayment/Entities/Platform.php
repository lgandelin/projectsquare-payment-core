<?php

namespace Webaccess\ProjectSquarePayment\Entities;

class Platform
{
    private $id;
    private $usersCount;
    private $fixedMonthlyCost;
    private $userMonthlyCost;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsersCount()
    {
        return $this->usersCount;
    }

    public function setUsersCount($usersCount)
    {
        $this->usersCount = $usersCount;
    }

    public function getFixedMonthlyCost()
    {
        return $this->fixedMonthlyCost;
    }

    public function setFixedMonthlyCost($fixedMonthlyCost)
    {
        $this->fixedMonthlyCost = $fixedMonthlyCost;
    }

    public function getUserMonthlyCost()
    {
        return $this->userMonthlyCost;
    }

    public function setUserMonthlyCost($userMonthlyCost)
    {
        $this->userMonthlyCost = $userMonthlyCost;
    }
}