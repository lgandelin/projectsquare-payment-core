<?php

namespace Webaccess\ProjectSquarePayment\Repositories;

use Webaccess\ProjectSquarePayment\Entities\Platform;

interface RemotePlatformRepository
{
    /**
     * @param Platform $platform
     * @return mixed
     */
    public function getUsersLimit(Platform $platform);

    public function updateUsersLimit(Platform $platform, $usersCount);
}