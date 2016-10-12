<?php

namespace Webaccess\ProjectSquarePayment\Responses\Payment;

use Webaccess\ProjectSquarePayment\Responses\Response;

class HandleBankCallResponse extends Response
{
    const TRANSACTION_NOT_FOUND_ERROR_CODE = -24;
    const INVALID_AMOUNT_ERROR_CODE = -25;
    const SIGNATURE_CHECK_FAILED_ERROR_CODE = -26;
    const BANK_RESPONSE_CODE_INVALID_ERROR_CODE = -27;

    public $success;
    public $errorCode;
}