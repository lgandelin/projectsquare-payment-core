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
        elseif (!$this->isTransactionAlreadyBeenProcessed($transaction)) {
            $this->updateTransactionAfterSuccess($request, $transaction);

            $responseFundPlatform = (new FundPlatformAccountInteractor($this->platformRepository))->execute(new FundPlatformAccountRequest([
                'platformID' => $transaction->getPlatformID(),
                'amount' => $request->amount,
            ]));

            if (!$responseFundPlatform->success)
                $errorCode = $responseFundPlatform->errorCode;
        }

        if ($errorCode != null && $transaction)
            $this->updateTransactionAfterError($transaction);

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
     * @param $transaction
     * @return bool
     */
    private function isTransactionAlreadyBeenProcessed(Transaction $transaction)
    {
        return $transaction->getStatus() !== Transaction::TRANSACTION_STATUS_IN_PROGRESS;
    }

    /**
     * @param HandleBankCallRequest $request
     * @param Transaction $transaction
     */
    private function updateTransactionAfterSuccess(HandleBankCallRequest $request, Transaction $transaction)
    {
        $transaction->setPaymentMean($request->parameters['paymentMeanType'] . ' - ' . $request->parameters['paymentMeanBrand']);
        $transaction->setResponseCode($request->parameters['responseCode']);
        $transaction->setStatus(Transaction::TRANSACTION_STATUS_VALIDATED);
        $this->transactionRepository->persist($transaction);
    }

    /**
     * @param Transaction $transaction
     */
    private function updateTransactionAfterError(Transaction $transaction)
    {
        $transaction->setStatus(Transaction::TRANSACTION_STATUS_ERROR);
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
}