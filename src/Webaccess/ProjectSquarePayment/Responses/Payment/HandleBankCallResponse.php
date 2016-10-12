<?php

namespace Webaccess\ProjectSquarePayment\Responses\Payment;

use Webaccess\ProjectSquarePayment\Responses\Response;

class HandleBankCallResponse extends Response
{
    const TRANSACTION_NOT_FOUND_ERROR_CODE = -1;
    const INVALID_AMOUNT_ERROR_CODE = -2;
    const SIGNATURE_CHECK_FAILED_ERROR_CODE = -3;

    public $success;
    public $errorCode;
}