<?php

namespace Webaccess\ProjectSquarePayment\Contracts;

interface BankService
{
    public function checkSignature($data, $seal);
}