<?php

namespace Webaccess\ProjectSquarePayment\Services;

interface ProjectsquareAPI
{
    public function getUsersLimit($platformID);

    public function updateUsersLimit($platformID, $usersCount);
}