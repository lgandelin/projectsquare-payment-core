<?php

namespace Webaccess\ProjectSquarePayment\Responses\Platforms;

use Webaccess\ProjectSquarePayment\Responses\Response;

class FundPlatformAccountResponse extends Response
{
    const PLATFORM_NOT_FOUND_ERROR_CODE = -1;
    const INVALID_AMOUNT_ERROR_CODE = -2;
    const REPOSITORY_UPDATE_FAILED = -3;

    public $success;
    public $errorCode;
}