<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Contracts\RemotePlatformService;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\UpdatePlatformUsersCountRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\UpdatePlatformUsersCountResponse;

class UpdatePlatformUsersCountInteractor
{
    private $platformRepository;
    private $remotePlatformService;
    private $logger;

    /**
     * @param PlatformRepository $platformRepository
     * @param RemotePlatformService $remotePlatformService
     * @param Logger $logger
     */
    public function __construct(PlatformRepository $platformRepository, RemotePlatformService $remotePlatformService, Logger $logger)
    {
        $this->platformRepository = $platformRepository;
        $this->remotePlatformService = $remotePlatformService;
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

        $remotePlatformUsersCount = $this->remotePlatformService->getUsersLimit($platform);

        if (!$this->isActualUsersCountValid($remotePlatformUsersCount))
            $errorCode = UpdatePlatformUsersCountResponse::INVALID_ACTUAL_USERS_COUNT;
        elseif(!$this->isUsersCountGreaterThanActualUsersCount($request->usersCount, $remotePlatformUsersCount)) {
            $errorCode = UpdatePlatformUsersCountResponse::ACTUAL_USERS_COUNT_TOO_BIG_ERROR;
        } else {
            $platform->setUsersCount($request->usersCount);
            $this->platformRepository->persist($platform);
            $this->remotePlatformService->updateUsersLimit($platform, $request->usersCount);
        }

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

    /**
     * @param $actualUsersCount
     * @return bool
     */
    private function isActualUsersCountValid($actualUsersCount)
    {
        return $actualUsersCount !== null && $actualUsersCount > 0;
    }

    /**
     * @param $usersCount
     * @param $actualUsersCount
     * @return bool
     */
    private function isUsersCountGreaterThanActualUsersCount($usersCount, $actualUsersCount)
    {
        return $usersCount >= $actualUsersCount;
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