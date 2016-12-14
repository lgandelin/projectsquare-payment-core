<?php

namespace Webaccess\ProjectSquarePayment\Repositories;

interface TransactionRepository
{
    public function getByIdentifier($transactionIdentifier);
}