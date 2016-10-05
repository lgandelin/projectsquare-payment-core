<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Repositories\RemotePlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\UpdatePlatformUsersCountRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\UpdatePlatformUsersCountResponse;

class UpdatePlatformUsersCountInteractor
{
    private $platformRepository;
    private $remotePlateformRepository;

    /**
     * @param PlatformRepository $platformRepository
     * @param RemotePlatformRepository $remotePlatformRepository
     */
    public function __construct(PlatformRepository $platformRepository, RemotePlatformRepository $remotePlatformRepository)
    {
        $this->platformRepository = $platformRepository;
        $this->remotePlateformRepository = $remotePlatformRepository;
    }

    /**
     * @param UpdatePlatformUsersCountRequest $request
     * @return UpdatePlatformUsersCountResponse
     */
    public function execute(UpdatePlatformUsersCountRequest $request)
    {
        $errorCode = null;

        if (!$platform = $this->platformRepository->getByID($request->platformID))
            $errorCode = UpdatePlatformUsersCountResponse::PLATFORM_NOT_FOUND_ERROR_CODE;
        elseif(!$this->isUsersCountValid($request->usersCount))
            $errorCode = UpdatePlatformUsersCountResponse::INVALID_USERS_COUNT;

        if ($errorCode !== null) return $this->createErrorResponse($errorCode);

        $remotePlatformUsersCount = $this->remotePlateformRepository->getUsersLimit($platform);

        if (!$this->isActualUsersCountValid($remotePlatformUsersCount))
            $errorCode = UpdatePlatformUsersCountResponse::INVALID_ACTUAL_USERS_COUNT;
        elseif(!$this->isUsersCountGreaterThanActualUsersCount($request->usersCount, $remotePlatformUsersCount)) {
            $errorCode = UpdatePlatformUsersCountResponse::ACTUAL_USERS_COUNT_TOO_BIG_ERROR;
        } else {
            $platform->setUsersCount($request->usersCount);
            $this->platformRepository->persist($platform);
            $this->remotePlateformRepository->updateUsersLimit($platform, $request->usersCount);
        }

        return ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);
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