<?php

namespace Webaccess\ProjectSquarePayment\Responses\Platforms;

use Webaccess\ProjectSquarePayment\Responses\Response;

class FundPlatformAccountResponse extends Response
{
    const PLATFORM_NOT_FOUND_ERROR_CODE = -17;
    const INVALID_AMOUNT_ERROR_CODE = -18;
    const REPOSITORY_UPDATE_FAILED = -19;

    public $success;
    public $errorCode;
}