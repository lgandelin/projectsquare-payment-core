<?php

namespace Webaccess\ProjectSquarePayment\Entities;

class Platform
{
    const PLATFORM_STATUS_DISABLED = 0;
    const PLATFORM_STATUS_TRIAL_PERIOD = 1;
    const PLATFORM_STATUS_IN_USE = 2;

    private $id;
    private $name;
    private $slug;
    private $usersCount;
    private $status;
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

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
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