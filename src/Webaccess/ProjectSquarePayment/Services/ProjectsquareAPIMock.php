<?php

namespace Webaccess\ProjectSquarePayment\Services;

class ProjectsquareAPIMock implements ProjectsquareAPI
{
    private $usersCount;

    public function __construct($usersCount)
    {
        $this->usersCount = $usersCount;
    }

    public function getUsersLimit($platformID)
    {
        return $this->usersCount;
    }

    public function updateUsersLimit($platformID, $usersCount)
    {
        $this->usersCount = $usersCount;
    }

}