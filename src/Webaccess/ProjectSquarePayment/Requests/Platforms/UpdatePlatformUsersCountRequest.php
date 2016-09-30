<?php

namespace Webaccess\ProjectSquarePayment\Requests\Platforms;

use Webaccess\ProjectSquarePayment\Requests\Request;

class UpdatePlatformUsersCountRequest extends Request
{
    public $platformID;
    public $usersCount;
    public $actualUsersCount;
}