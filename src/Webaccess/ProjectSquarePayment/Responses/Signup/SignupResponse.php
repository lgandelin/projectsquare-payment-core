<?php

namespace Webaccess\ProjectSquarePayment\Responses\Signup;

use Webaccess\ProjectSquarePayment\Responses\Response;

class SignupResponse extends Response
{
    public $success;
    public $errorCode;
    public $platformID;
    public $administratorID;
}