<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Payment;

use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Payment\FundPlatformAccountRequest;
use Webaccess\ProjectSquarePayment\Responses\Payment\FundPlatformAccountResponse;

class FundPlatformAccountInteractor
{
    private $platformRepository;

    /**
     * @param PlatformRepository $platformRepository
     * @param Logger $logger
     */
    public function __construct(PlatformRepository $platformRepository, Logger $logger)
    {
        $this->platformRepository = $platformRepository;
        $this->logger = $logger;
    }

    /**
     * @param FundPlatformAccountRequest $request
     * @return FundPlatformAccountResponse
     */
    public function execute(FundPlatformAccountRequest $request)
    {
        $this->logger->logRequest(self::class, $request);

        $errorCode = null;

        if (!$platform = $this->platformRepository->getByID($request->platformID))
            $errorCode = FundPlatformAccountResponse::PLATFORM_NOT_FOUND_ERROR_CODE;

        elseif (!$this->isAmountValid($request->amount))
            $errorCode = FundPlatformAccountResponse::INVALID_AMOUNT_ERROR_CODE;
        
        else {
            $platform->setAccountBalance($platform->getAccountBalance() + $request->amount);

            if (!$this->platformRepository->persist($platform)) {
                $errorCode = FundPlatformAccountResponse::REPOSITORY_UPDATE_FAILED;
            }
        }

        $response = ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);

        $this->logger->logResponse(self::class, $response);

        return $response;
    }

    /**
     * @param $errorCode
     * @return FundPlatformAccountResponse
     */
    private function createErrorResponse($errorCode)
    {
        return new FundPlatformAccountResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }

    /**
     * @return FundPlatformAccountResponse
     */
    private function createSuccessResponse()
    {
        return new FundPlatformAccountResponse([
            'success' => true
        ]);
    }

    /**
     * @param $amount
     * @return bool
     */
    private function isAmountValid($amount)
    {
        return $amount > 0;
    }
}