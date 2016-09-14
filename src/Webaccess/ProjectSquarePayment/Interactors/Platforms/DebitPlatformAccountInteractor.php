<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\DebitPlatformAccountRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\DebitPlatformAccountResponse;

class DebitPlatformAccountInteractor
{
    private $platformRepository;

    /**
     * @param PlatformRepository $platformRepository
     */
    public function __construct(PlatformRepository $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    public function execute(DebitPlatformAccountRequest $request): DebitPlatformAccountResponse
    {
        $errorCode = null;

        if (!$platform = $this->platformRepository->getByID($request->platformID))
            $errorCode = DebitPlatformAccountResponse::PLATFORM_NOT_FOUND_ERROR_CODE;
        else {
            $amount = (new GetPlatformUsageAmountInteractor($this->platformRepository))->getDailyCost($platform->getID());
            $platform->setAccountBalance($platform->getAccountBalance() - $amount);

            if (!$this->platformRepository->persist($platform)) {
                $errorCode = DebitPlatformAccountResponse::REPOSITORY_UPDATE_FAILED;
            }
        }

        return ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);
    }

    private function createErrorResponse($errorCode)
    {
       return new DebitPlatformAccountResponse([
            'success' => false,
            'errorCode' => $errorCode
       ]);
    }

    private function createSuccessResponse()
    {
        return new DebitPlatformAccountResponse([
            'success' => true
        ]);
    }
}