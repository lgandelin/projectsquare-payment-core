<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\FundPlatformAccountRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\FundPlatformAccountResponse;

class FundPlatformAccountInteractor
{
    private $platformRepository;

    /**
     * @param PlatformRepository $platformRepository
     */
    public function __construct(PlatformRepository $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    public function execute(FundPlatformAccountRequest $request)
    {
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

        return ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);
    }

    private function createErrorResponse($errorCode)
    {
        return new FundPlatformAccountResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }

    private function createSuccessResponse()
    {
        return new FundPlatformAccountResponse([
            'success' => true
        ]);
    }

    private function isAmountValid($amount)
    {
        return $amount > 0;
    }
}