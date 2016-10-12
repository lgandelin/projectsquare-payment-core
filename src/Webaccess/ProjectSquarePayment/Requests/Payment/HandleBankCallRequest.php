<?php

namespace Webaccess\ProjectSquarePayment\Requests\Payment;

use Webaccess\ProjectSquarePayment\Requests\Request;

class HandleBankCallRequest extends Request
{
    public $transactionIdentifier;
    public $amount;
    public $bankParameters;
    public $seal;
}