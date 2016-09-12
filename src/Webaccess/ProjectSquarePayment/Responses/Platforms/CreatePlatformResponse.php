<?php

namespace Webaccess\ProjectSquarePayment\Responses\Platforms;

use Webaccess\ProjectSquarePayment\Responses\Response;

class CreatePlatformResponse extends Response
{
    const REPOSITORY_INSERTION_FAILED_ERROR_CODE = -1;
    const PLATFORM_NAME_REQUIRED_ERROR_CODE = -2;

    public $platform;
    public $success;
    public $errorCode;
}