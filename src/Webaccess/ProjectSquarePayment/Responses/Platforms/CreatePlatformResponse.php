<?php

namespace Webaccess\ProjectSquarePayment\Responses\Platforms;

use Webaccess\ProjectSquarePayment\Responses\Response;

class CreatePlatformResponse extends Response
{
    const REPOSITORY_CREATION_FAILED = -1;
    const PLATFORM_NAME_REQUIRED = -2;
    const PLATFORM_SLUG_REQUIRED = -3;
    const PLATFORM_SLUG_UNAVAILABLE = -4;
    const PLATFORM_USERS_COUNT_REQUIRED = -5;

    public $success;
    public $errorCode;
    public $platform;
}