<?php

namespace Webaccess\ProjectSquarePayment\Contracts;

use Webaccess\ProjectSquarePayment\Entities\Platform;

interface RemotePlatformService
{
    public function getPlatformURL(Platform $platform);

    public function getUsersLimit(Platform $platform);

    public function updateUsersLimit(Platform $platform, $usersCount);
}