<?php

namespace Webaccess\ProjectSquarePayment\Responses\Signup;

use Webaccess\ProjectSquarePayment\Responses\Response;

class CheckPlatformSlugResponse extends Response
{
    const PLATFORM_SLUG_UNAVAILABLE = -5;
    const PLATFORM_SLUG_INVALID = -6;

    public $success;
    public $errorCode;
}