<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\DebitPlatformAccountRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\DebitPlatformAccountResponse;

class DebitPlatformAccountInteractor
{
    private $platformRepository;
    private $logger;

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
     * @param DebitPlatformAccountRequest $request
     * @return DebitPlatformAccountResponse
     */
    public function execute(DebitPlatformAccountRequest $request)
    {
        $this->logger->logRequest(self::class, $request);

        $errorCode = null;

        if (!$platform = $this->platformRepository->getByID($request->platformID))
            $errorCode = DebitPlatformAccountResponse::PLATFORM_NOT_FOUND_ERROR_CODE;
        else {
            $amount = (new GetPlatformUsageAmountInteractor($this->platformRepository, $this->logger))->getDailyCost($platform->getID());
            $platform->setAccountBalance($platform->getAccountBalance() - $amount);

            if (!$this->platformRepository->persist($platform)) {
                $errorCode = DebitPlatformAccountResponse::REPOSITORY_UPDATE_FAILED;
            }
        }

        $response = ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);

        $this->logger->logResponse(self::class, $response);

        return $response;
    }

    /**
     * @param $errorCode
     * @return DebitPlatformAccountResponse
     */
    private function createErrorResponse($errorCode)
    {
       return new DebitPlatformAccountResponse([
            'success' => false,
            'errorCode' => $errorCode
       ]);
    }

    /**
     * @return DebitPlatformAccountResponse
     */
    private function createSuccessResponse()
    {
        return new DebitPlatformAccountResponse([
            'success' => true
        ]);
    }
}