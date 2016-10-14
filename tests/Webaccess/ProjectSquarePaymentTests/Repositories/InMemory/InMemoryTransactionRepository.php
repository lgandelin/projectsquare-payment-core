<?php

namespace Webaccess\ProjectSquarePaymentTests\Repositories\InMemory;

use Webaccess\ProjectSquarePayment\Entities\Transaction;
use Webaccess\ProjectSquarePayment\Repositories\TransactionRepository;

class InMemoryTransactionRepository implements TransactionRepository
{
    public $objects;

    public function __construct()
    {
        $this->objects = [];
    }

    private function getNextID()
    {
        return sizeof($this->objects) + 1;
    }

    public function getByID($transactionID)
    {
        return (isset($this->objects[$transactionID])) ? clone $this->objects[$transactionID] : false;
    }

    public function persist(Transaction $transaction)
    {
        if (!$transaction->getID()) {
            $transaction->setId($this->getNextID());
        }
        $this->objects[$transaction->getID()] = $transaction;

        return true;
    }

    public function getByIdentifier($transactionIdentifier)
    {
        foreach ($this->objects as $transaction) {
            if ($transaction->getIdentifier() === $transactionIdentifier) {
                return $transaction;
            }
        }

        return false;
    }
}