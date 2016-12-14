<?php

namespace Webaccess\ProjectSquarePayment\Responses\Platforms;

use Webaccess\ProjectSquarePayment\Responses\Response;

class DebitPlatformAccountResponse extends Response
{
    const PLATFORM_NOT_FOUND_ERROR_CODE = -14;
    const INVALID_AMOUNT_ERROR_CODE = -15;
    const REPOSITORY_UPDATE_FAILED = -16;

    public $success;
    public $errorCode;
}