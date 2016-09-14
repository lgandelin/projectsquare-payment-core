<?php

namespace Webaccess\ProjectSquarePayment\Responses\Platforms;

use Webaccess\ProjectSquarePayment\Responses\Response;

class DebitPlatformAccountResponse extends Response
{
    const PLATFORM_NOT_FOUND_ERROR_CODE = -1;
    const INVALID_AMOUNT_ERROR_CODE = -2;

    public $success;
    public $errorCode;
}