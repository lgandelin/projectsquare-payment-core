<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\UpdatePlatformUsersCountRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\UpdatePlatformUsersCountResponse;

class UpdatePlatformUsersCountInteractor
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
     * @param UpdatePlatformUsersCountRequest $request
     * @return UpdatePlatformUsersCountResponse
     */
    public function execute(UpdatePlatformUsersCountRequest $request)
    {
        $this->logger->logRequest(self::class, $request);

        $errorCode = null;

        if (!$platform = $this->platformRepository->getByID($request->platformID))
            $errorCode = UpdatePlatformUsersCountResponse::PLATFORM_NOT_FOUND_ERROR_CODE;
        elseif(!$this->isUsersCountValid($request->usersCount))
            $errorCode = UpdatePlatformUsersCountResponse::INVALID_USERS_COUNT;

        if ($errorCode !== null) return $this->createErrorResponse($errorCode);

        $platform->setUsersCount($request->usersCount);

        $this->platformRepository->persist($platform);

        $response = ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);

        $this->logger->logResponse(self::class, $response);

        return $response;
    }

    /**
     * @param $usersCount
     * @return bool
     */
    private function isUsersCountValid($usersCount)
    {
        return $usersCount > 0;
    }

    private function createSuccessResponse()
    {
        return new UpdatePlatformUsersCountResponse([
            'success' => true,
        ]);
    }

    private function createErrorResponse($errorCode)
    {
        return new UpdatePlatformUsersCountResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }
}