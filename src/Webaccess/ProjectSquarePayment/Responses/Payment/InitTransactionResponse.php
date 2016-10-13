<?php

namespace Webaccess\ProjectSquarePayment\Responses\Payment;

use Webaccess\ProjectSquarePayment\Responses\Response;

class InitTransactionResponse extends Response
{
    const PLATFORM_NOT_FOUND_ERROR = -1;
    const INVALID_AMOUNT_ERROR = -2;

    public $success;
    public $errorCode;
    public $transactionIdentifier;
    public $data;
    public $seal;
}