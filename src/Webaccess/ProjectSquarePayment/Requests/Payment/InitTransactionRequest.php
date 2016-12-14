<?php

namespace Webaccess\ProjectSquarePayment\Requests\Payment;

use Webaccess\ProjectSquarePayment\Requests\Request;

class InitTransactionRequest extends Request
{
    public $platformID;
    public $amount;
}