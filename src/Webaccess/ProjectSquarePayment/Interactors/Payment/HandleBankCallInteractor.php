<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Payment;

use Webaccess\ProjectSquarePayment\Entities\Transaction;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Repositories\TransactionRepository;
use Webaccess\ProjectSquarePayment\Requests\Payment\FundPlatformAccountRequest;
use Webaccess\ProjectSquarePayment\Requests\Payment\HandleBankCallRequest;
use Webaccess\ProjectSquarePayment\Responses\Payment\HandleBankCallResponse;
use Webaccess\ProjectSquarePayment\Services\BankService;

class HandleBankCallInteractor
{
    private $platformRepository;
    private $transactionRepository;
    private $bankService;

    /**
     * @param PlatformRepository $platformRepository
     * @param TransactionRepository $transactionRepository
     * @param BankService $bankService
     */
    public function __construct(PlatformRepository $platformRepository, TransactionRepository $transactionRepository, BankService $bankService)
    {
        $this->platformRepository = $platformRepository;
        $this->transactionRepository = $transactionRepository;
        $this->bankService = $bankService;
    }

    /**
     * @param HandleBankCallRequest $request
     * @return HandleBankCallResponse
     */
    public function execute(HandleBankCallRequest $request)
    {
        $errorCode = null;
        if (!$transaction = $this->transactionRepository->getByIdentifier($request->transactionIdentifier))
            $errorCode = HandleBankCallResponse::TRANSACTION_NOT_FOUND_ERROR_CODE;
        elseif (!$this->checkTransactionAmount($transaction, $request->amount))
            $errorCode = HandleBankCallResponse::INVALID_AMOUNT_ERROR_CODE;
        elseif (!$this->checkSignature($request->data, $request->seal))
            $errorCode = HandleBankCallResponse::SIGNATURE_CHECK_FAILED_ERROR_CODE;
        elseif (!$this->checkBankReponseCode($request->parameters))
            $errorCode = HandleBankCallResponse::BANK_RESPONSE_CODE_INVALID_ERROR_CODE;
        elseif (!$this->isTransactionAlreadyBeenProcessed($transaction)) {
            $this->updateTransactionStatus($transaction, Transaction::TRANSACTION_STATUS_VALIDATED);

            $responseFundPlatform = (new FundPlatformAccountInteractor($this->platformRepository))->execute(new FundPlatformAccountRequest([
                'platformID' => $transaction->getPlatformID(),
                'amount' => $request->amount,
            ]));

            if (!$responseFundPlatform->success)
                $errorCode = $responseFundPlatform->errorCode;
        }

        if ($transaction) {
            $this->updateTransactionData($request, $transaction);

            if ($errorCode != null)
                $this->updateTransactionStatus($transaction, Transaction::TRANSACTION_STATUS_ERROR);
        }

        return ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);
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
     * @return HandleBankCallResponse
     */
    private function createSuccessResponse()
    {
        return new HandleBankCallResponse([
            'success' => true,
        ]);
    }

    /**
     * @param $errorCode
     * @return HandleBankCallResponse
     */
    private function createErrorResponse($errorCode)
    {
        return new HandleBankCallResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }

    /**
     * @param HandleBankCallRequest $request
     * @param $transaction
     */
    private function updateTransactionData(HandleBankCallRequest $request, $transaction)
    {
        $transaction->setPaymentMean($request->parameters['paymentMeanType'] . ' - ' . $request->parameters['paymentMeanBrand']);
        $transaction->setResponseCode($request->parameters['responseCode']);
    }
}