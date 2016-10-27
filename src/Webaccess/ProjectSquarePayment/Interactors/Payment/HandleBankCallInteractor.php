<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Payment;

use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Entities\Transaction;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Repositories\TransactionRepository;
use Webaccess\ProjectSquarePayment\Requests\Payment\FundPlatformAccountRequest;
use Webaccess\ProjectSquarePayment\Requests\Payment\HandleBankCallRequest;
use Webaccess\ProjectSquarePayment\Responses\Payment\HandleBankCallResponse;
use Webaccess\ProjectSquarePayment\Contracts\BankService;

class HandleBankCallInteractor
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
     * @param HandleBankCallRequest $request
     * @return HandleBankCallResponse
     */
    public function execute(HandleBankCallRequest $request)
    {
        $this->logger->logRequest(self::class, $request);

        $errorCode = null;

        list($parameters, $transactionIdentifier, $amount) = $this->extractParameters($request);

        if (!$transaction = $this->transactionRepository->getByIdentifier($transactionIdentifier))
            $errorCode = HandleBankCallResponse::TRANSACTION_NOT_FOUND_ERROR_CODE;

        elseif (!$this->checkTransactionAmount($transaction, $amount))
            $errorCode = HandleBankCallResponse::INVALID_AMOUNT_ERROR_CODE;

        elseif (!$this->checkSignature($request->data, $request->seal))
            $errorCode = HandleBankCallResponse::SIGNATURE_CHECK_FAILED_ERROR_CODE;

        elseif (!$this->checkBankReponseCode($parameters))
            $errorCode = HandleBankCallResponse::BANK_RESPONSE_CODE_INVALID_ERROR_CODE;

        elseif (!$this->isTransactionAlreadyBeenProcessed($transaction)) {
            $this->updateTransactionStatus($transaction, Transaction::TRANSACTION_STATUS_VALIDATED);

            $responseFundPlatform = (new FundPlatformAccountInteractor($this->platformRepository, $this->logger))->execute(new FundPlatformAccountRequest([
                'platformID' => $transaction->getPlatformID(),
                'amount' => $amount,
            ]));

            if (!$responseFundPlatform->success)
                $errorCode = $responseFundPlatform->errorCode;
        }

        if ($transaction) {
            $this->updateTransactionData($transaction, $parameters);

            if ($errorCode != null)
                $this->updateTransactionStatus($transaction, Transaction::TRANSACTION_STATUS_ERROR);
        }

        $response = ($errorCode === null) ? $this->createSuccessResponse($transactionIdentifier) : $this->createErrorResponse($errorCode, $transactionIdentifier);

        $this->logger->logResponse(self::class, $response);

        return $response;
    }

    /**
     * @param Transaction $transaction
     * @param $amount
     * @return bool
     */
    private function checkTransactionAmount(Transaction $transaction, $amount)
    {
        return $transaction->getAmount() == $amount;
    }

    /**
     * @param $data
     * @param $seal
     * @return mixed
     */
    private function checkSignature($data, $seal)
    {
        return $this->bankService->checkSignature($data, $seal);
    }

    /**
     * @param $parameters
     * @return bool
     */
    private function checkBankReponseCode($parameters)
    {
        return $parameters['responseCode'] == '00';
    }

    /**
     * @param $transaction
     * @return bool
     */
    private function isTransactionAlreadyBeenProcessed(Transaction $transaction)
    {
        return $transaction->getStatus() !== Transaction::TRANSACTION_STATUS_IN_PROGRESS;
    }

    /**
     * @param Transaction $transaction
     * @param $status
     */
    private function updateTransactionStatus(Transaction $transaction, $status)
    {
        $transaction->setStatus($status);
        $this->transactionRepository->persist($transaction);
    }

    /**
     * @param Transaction $transaction
     * @param $parameters
     */
    private function updateTransactionData(Transaction $transaction, $parameters)
    {
        $transaction->setPaymentMean($parameters['paymentMeanType'] . ' - ' . $parameters['paymentMeanBrand']);
        $transaction->setResponseCode($parameters['responseCode']);
        $this->transactionRepository->persist($transaction);
    }

    /**
     * @param $transactionIdentifier
     * @return HandleBankCallResponse
     */
    private function createSuccessResponse($transactionIdentifier)
    {
        return new HandleBankCallResponse([
            'success' => true,
            'transactionIdentifier' => $transactionIdentifier
        ]);
    }

    /**
     * @param $errorCode
     * @param $transactionIdentifier
     * @return HandleBankCallResponse
     */
    private function createErrorResponse($errorCode, $transactionIdentifier)
    {
        return new HandleBankCallResponse([
            'success' => false,
            'errorCode' => $errorCode,
            'transactionIdentifier' => $transactionIdentifier
        ]);
    }

    /**
     * @param HandleBankCallRequest $request
     * @return array
     */
    private function extractParameters(HandleBankCallRequest $request)
    {
        $parameters = $this->bankService->extractParametersFromData($request->data);
        $transactionIdentifier = $parameters['transactionReference'];
        $amount = floatval($parameters['amount']) / 100;

        return array($parameters, $transactionIdentifier, $amount);
    }
}