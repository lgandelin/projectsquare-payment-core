<?php

namespace Webaccess\ProjectSquarePayment\Contracts;

interface BankService
{
    public function generateFormFields($transactionIdentifier, $amount);

    public function checkSignature($data, $seal);

    public function extractParametersFromData($data);
}