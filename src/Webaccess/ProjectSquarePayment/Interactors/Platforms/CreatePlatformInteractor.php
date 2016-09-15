<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use DateTime;
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

        elseif (!$platform->getSlug())
            $errorCode = CreatePlatformResponse::PLATFORM_SLUG_REQUIRED;

        elseif (!$this->isSlugAvailable($platform->getSlug()))
            $errorCode = CreatePlatformResponse::PLATFORM_SLUG_UNAVAILABLE;

        elseif (!$this->isSlugValid($platform->getSlug()))
            $errorCode = CreatePlatformResponse::PLATFORM_SLUG_INVALID;

        elseif (!$platform->getUsersCount())
            $errorCode = CreatePlatformResponse::PLATFORM_USERS_COUNT_REQUIRED;

        elseif (!$platformID = $this->platformRepository->persist($platform))
            $errorCode = CreatePlatformResponse::REPOSITORY_CREATION_FAILED;

        return ($errorCode === null) ? $this->createSuccessResponse($platformID) : $this->createErrorResponse($errorCode);
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
        $platform->setCreationDate(new DateTime());

        return $platform;
    }

    /**
     * @param $platformSlug
     * @return bool
     */
    private function isSlugAvailable($platformSlug)
    {
        return !$this->platformRepository->getBySlug($platformSlug);
    }

    /**
     * @param $platformSlug
     * @return int
     */
    private function isSlugValid($platformSlug)
    {
        return preg_match('/^[a-z0-9](-?[a-z0-9]+)*$/i', $platformSlug);
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
     * @param $platformID
     * @return CreatePlatformResponse
     */
    private function createSuccessResponse($platformID): CreatePlatformResponse
    {
        return new CreatePlatformResponse([
            'success' => true,
            'platformID' => $platformID
        ]);
    }
}