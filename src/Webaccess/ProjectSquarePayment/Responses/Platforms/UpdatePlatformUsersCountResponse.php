<?php

namespace Webaccess\ProjectSquarePayment\Responses\Platforms;

use Webaccess\ProjectSquarePayment\Responses\Response;

class UpdatePlatformUsersCountResponse extends Response
{
    const PLATFORM_NOT_FOUND_ERROR_CODE = -1;
    const ACTUAL_USERS_COUNT_TOO_BIG_ERROR = -2;
    const INVALID_USERS_COUNT = -3;

    public $success;
    public $errorCode;
}