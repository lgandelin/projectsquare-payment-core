<?php

namespace Webaccess\ProjectSquarePayment\Services;

interface BankService
{
    public function checkSignature($parameters, $seal);
}