<?php

namespace Webaccess\ProjectSquarePayment\Entities;

class Transaction
{
    const TRANSACTION_STATUS_IN_PROGRESS = 1;
    const TRANSACTION_STATUS_VALIDATED = 2;
    const TRANSACTION_STATUS_ERROR = 3;
    const TRANSACTION_STATUS_CANCELED = 4;

    private $id;
    private $identifier;
    private $amount;
    private $platformID;
    private $paymentMean;
    private $status;
    private $responseCode;
    private $creationDate;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getPaymentMean()
    {
        return $this->paymentMean;
    }

    public function setPaymentMean($paymentMean)
    {
        $this->paymentMean = $paymentMean;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }

    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
    }

    public function getPlatformID()
    {
        return $this->platformID;
    }

    public function setPlatformID($platformID)
    {
        $this->platformID = $platformID;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }
}