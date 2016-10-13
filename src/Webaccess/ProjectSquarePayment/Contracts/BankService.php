<?php

namespace Webaccess\ProjectSquarePayment\Contracts;

interface BankService
{
    public function generateFormFields($transactionIdentifier, $amount);

    public function checkSignature($data, $seal);
}