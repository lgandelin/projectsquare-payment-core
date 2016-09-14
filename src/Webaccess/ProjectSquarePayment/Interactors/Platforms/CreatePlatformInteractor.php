<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\CreatePlatformRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse;

class CreatePlatformInteractor
{
    private $platformRepository;

    /**
     * @param PlatformRepository $platformRepository
     */
    public function __construct(PlatformRepository $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    /**
     * @param CreatePlatformRequest $request
     * @return CreatePlatformResponse
     */
    public function execute(CreatePlatformRequest $request): CreatePlatformResponse
    {
        $errorCode = null;
        $platform = $this->createObjectFromRequest($request);

        if (!$platform->getName())
            $errorCode = CreatePlatformResponse::PLATFORM_NAME_REQUIRED;

        elseif (!$platform->getUsersCount())
            $errorCode = CreatePlatformResponse::PLATFORM_USERS_COUNT_REQUIRED;

        elseif (!$this->isSlugAvailable($platform))
            $errorCode = CreatePlatformResponse::PLATFORM_SLUG_UNAVAILABLE;

        elseif (!$this->platformRepository->persist($platform))
            $errorCode = CreatePlatformResponse::REPOSITORY_CREATION_FAILED;

        return ($errorCode === null) ? $this->createSuccessResponse($platform) : $this->createErrorResponse($errorCode);
    }

    /**
     * @param CreatePlatformRequest $request
     * @return Platform
     */
    private function createObjectFromRequest(CreatePlatformRequest $request): Platform
    {
        $platform = new Platform();
        $platform->setName($request->name);
        $platform->setSlug($request->slug);
        $platform->setUsersCount($request->usersCount);

        return $platform;
    }

    /**
     * @param Platform $platform
     * @return bool
     */
    private function isSlugAvailable(Platform $platform)
    {
        return !$this->platformRepository->getBySlug($platform->getSlug());
    }

    /**
     * @param $errorCode
     * @return CreatePlatformResponse
     */
    private function createErrorResponse($errorCode): CreatePlatformResponse
    {
        return new CreatePlatformResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }

    /**
     * @param $platform
     * @return CreatePlatformResponse
     */
    private function createSuccessResponse(Platform $platform): CreatePlatformResponse
    {
        return new CreatePlatformResponse([
            'success' => true,
            'platform' => $platform
        ]);
    }
}