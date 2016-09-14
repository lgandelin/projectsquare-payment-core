<?php

namespace Webaccess\ProjectSquarePayment\Requests\Platforms;

use Webaccess\ProjectSquarePayment\Requests\Request;

class FundPlatformAccountRequest extends Request
{
    public $platformID;
    public $amount;
}