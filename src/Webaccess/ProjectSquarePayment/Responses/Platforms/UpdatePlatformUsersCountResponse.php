<?php

namespace Webaccess\ProjectSquarePayment\Responses\Platforms;

use Webaccess\ProjectSquarePayment\Responses\Response;

class UpdatePlatformUsersCountResponse extends Response
{
    const PLATFORM_NOT_FOUND_ERROR_CODE = -20;
    const ACTUAL_USERS_COUNT_TOO_BIG_ERROR = -21;
    const INVALID_USERS_COUNT = -22;
    const INVALID_ACTUAL_USERS_COUNT = -23;

    public $success;
    public $errorCode;
}