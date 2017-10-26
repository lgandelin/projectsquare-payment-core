<?php

namespace Webaccess\ProjectSquarePayment\Responses\Platforms;

use Webaccess\ProjectSquarePayment\Responses\Response;

class UpdatePlatformUsersCountResponse extends Response
{
    const PLATFORM_NOT_FOUND_ERROR_CODE = -20;
    const INVALID_USERS_COUNT = -22;

    public $success;
    public $errorCode;
}