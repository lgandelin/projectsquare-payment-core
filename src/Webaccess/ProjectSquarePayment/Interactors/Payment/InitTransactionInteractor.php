<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Payment;

use Webaccess\ProjectSquarePayment\Contracts\BankService;
use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Entities\Transaction;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Repositories\TransactionRepository;
use Webaccess\ProjectSquarePayment\Requests\Payment\InitTransactionRequest;
use Webaccess\ProjectSquarePayment\Responses\Payment\InitTransactionResponse;

class InitTransactionInteractor
{
    private $platformRepository;
    private $transactionRepository;
    private $bankService;
    private $logger;

    /**
     * @param PlatformRepository $platformRepository
     * @param TransactionRepository $transactionRepository
     * @param BankService $bankService
     * @param Logger $logger
     */
    public function __construct(PlatformRepository $platformRepository, TransactionRepository $transactionRepository, BankService $bankService, Logger $logger)
    {
        $this->platformRepository = $platformRepository;
        $this->transactionRepository = $transactionRepository;
        $this->bankService = $bankService;
        $this->logger = $logger;
    }

    /**
     * @param InitTransactionRequest $request
     * @return InitTransactionResponse
     */
    public function execute(InitTransactionRequest $request)
    {
        $this->logger->logRequest(self::class, $request);

        $errorCode = null;

        if (!$this->checkPlatform($request->platformID))
            $errorCode = InitTransactionResponse::PLATFORM_NOT_FOUND_ERROR;
        elseif (!$this->checkAmount($request->amount))
            $errorCode = InitTransactionResponse::INVALID_AMOUNT_ERROR;
        else {
            $transactionIdentifier = $this->createTransaction($request);
            list($data, $seal) = $this->bankService-> generateFormFields($transactionIdentifier, $request->amount);
        }

        $response = ($errorCode === null) ? $this->createSuccessResponse($transactionIdentifier, $data, $seal) : $this->createErrorResponse($errorCode);

        $this->logger->logResponse(self::class, $response);

        return $response;
    }

    /**
     * @param $platformID
     * @return mixed
     */
    private function checkPlatform($platformID)
    {
        return $this->platformRepository->getByID($platformID);
    }

    /**
     * @param $amount
     * @return bool
     */
    private function checkAmount($amount)
    {
        return is_numeric($amount) && $amount > 0;
    }

    /**
     * @param $transactionIdentifier
     * @param $data
     * @param $seal
     * @return InitTransactionResponse
     */
    private function createSuccessResponse($transactionIdentifier, $data, $seal)
    {
        return new InitTransactionResponse([
            'success' => true,
            'transactionIdentifier' => $transactionIdentifier,
            'data' => $data,
            'seal' => $seal,
        ]);
    }

    /**
     * @param $errorCode
     * @return InitTransactionResponse
     */
    private function createErrorResponse($errorCode)
    {
        return new InitTransactionResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }

    private function generateNewIdentifier()
    {
        return uniqid();
    }

    /**
     * @param InitTransactionRequest $request
     * @return string
     */
    private function createTransaction(InitTransactionRequest $request)
    {
        $transaction = new Transaction();
        $transactionIdentifier = $this->generateNewIdentifier();
        $transaction->setIdentifier($transactionIdentifier);
        $transaction->setPlatformID($request->platformID);
        $transaction->setAmount($request->amount);
        $transaction->setStatus(Transaction::TRANSACTION_STATUS_IN_PROGRESS);
        $this->transactionRepository->persist($transaction);

        return $transactionIdentifier;
    }


}