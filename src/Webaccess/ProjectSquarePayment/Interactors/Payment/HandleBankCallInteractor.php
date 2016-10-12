<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Payment;

use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Repositories\TransactionRepository;
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

        if (!$transaction = $this->transactionRepository->getByIdentifier($request->transactionIdentifier)) {
            $errorCode = HandleBankCallResponse::TRANSACTION_NOT_FOUND_ERROR_CODE;
        } elseif (!$this->checkTransactionAmount($transaction, $request->amount)) {
            $errorCode = HandleBankCallResponse::INVALID_AMOUNT_ERROR_CODE;
        } elseif (!$this->checkSignature($request->bankParameters, $request->seal)) {
            $errorCode = HandleBankCallResponse::SIGNATURE_CHECK_FAILED_ERROR_CODE;
        }

        return ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);
    }

    private function checkTransactionAmount($transaction, $amount)
    {
        return $transaction->getAmount() == $amount;
    }

    private function checkSignature($bankParameters, $seal)
    {
        return $this->bankService->checkSignature($bankParameters, $seal);
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