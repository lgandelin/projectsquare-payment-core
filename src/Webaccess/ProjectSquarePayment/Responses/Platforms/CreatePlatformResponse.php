<?php

namespace Webaccess\ProjectSquarePayment\Responses\Platforms;

use Webaccess\ProjectSquarePayment\Responses\Response;

class CreatePlatformResponse extends Response
{
    const REPOSITORY_INSERTION_FAILED = -1;
    const PLATFORM_NAME_REQUIRED = -2;
    const PLATFORM_SLUG_UNAVAILABLE = -3;
    const PLATFORM_USERS_COUNT_REQUIRED = -4;

    public $success;
    public $errorCode;
    public $platform;
}