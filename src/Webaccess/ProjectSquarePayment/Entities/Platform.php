<?php

namespace Webaccess\ProjectSquarePayment\Entities;

class Platform
{
    private $id;
    private $name;
    private $slug;
    private $usersCount;
    private $platformMonthlyCost;
    private $userMonthlyCost;
    private $accountBalance;
    private $creationDate;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getUsersCount()
    {
        return $this->usersCount;
    }

    public function setUsersCount($usersCount)
    {
        $this->usersCount = $usersCount;
    }

    public function getPlatformMonthlyCost()
    {
        return $this->platformMonthlyCost;
    }

    public function setPlatformMonthlyCost($platformMonthlyCost)
    {
        $this->platformMonthlyCost = $platformMonthlyCost;
    }

    public function getUserMonthlyCost()
    {
        return $this->userMonthlyCost;
    }

    public function setUserMonthlyCost($userMonthlyCost)
    {
        $this->userMonthlyCost = $userMonthlyCost;
    }

    public function getAccountBalance()
    {
        return $this->accountBalance;
    }

    public function setAccountBalance($accountBalance)
    {
        $this->accountBalance = $accountBalance;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }
}