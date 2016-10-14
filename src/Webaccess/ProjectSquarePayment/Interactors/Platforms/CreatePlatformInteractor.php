<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use DateTime;
use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Interactors\Signup\CheckPlatformSlugInteractor;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\CreatePlatformRequest;
use Webaccess\ProjectSquarePayment\Requests\Signup\CheckPlatformSlugRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse;
use Webaccess\ProjectSquarePayment\Responses\Signup\CheckPlatformSlugResponse;

class CreatePlatformInteractor
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
     * @param CreatePlatformRequest $request
     * @return CreatePlatformResponse
     */
    public function execute(CreatePlatformRequest $request)
    {
        $this->logger->logRequest(self::class, $request);

        $errorCode = null;
        $platform = $this->createObjectFromRequest($request);
        $responseSlug = $this->verifyPlatformSlug($request);

        if (!$platform->getName())
            $errorCode = CreatePlatformResponse::PLATFORM_NAME_REQUIRED;

        elseif (!$platform->getSlug())
            $errorCode = CreatePlatformResponse::PLATFORM_SLUG_REQUIRED;

        elseif (!$responseSlug->success)
            $errorCode = $responseSlug->errorCode;

        elseif (!$platform->getUsersCount())
            $errorCode = CreatePlatformResponse::PLATFORM_USERS_COUNT_REQUIRED;

        elseif (!$platformID = $this->platformRepository->persist($platform))
            $errorCode = CreatePlatformResponse::REPOSITORY_CREATION_FAILED;

        $response = ($errorCode === null) ? $this->createSuccessResponse($platformID) : $this->createErrorResponse($errorCode);

        $this->logger->logResponse(self::class, $response);

        return $response;
    }

    /**
     * @param CreatePlatformRequest $request
     * @return Platform
     */
    private function createObjectFromRequest(CreatePlatformRequest $request)
    {
        $platform = new Platform();
        $platform->setName($request->name);
        $platform->setSlug($request->slug);
        $platform->setUsersCount($request->usersCount);
        $platform->setStatus(Platform::PLATFORM_STATUS_TRIAL_PERIOD);
        $platform->setPlatformMonthlyCost($request->platformMonthlyCost);
        $platform->setUserMonthlyCost($request->userMonthlyCost);
        $platform->setCreationDate(new DateTime());

        return $platform;
    }

    /**
     * @param CreatePlatformRequest $request
     * @return CheckPlatformSlugResponse
     */
    private function verifyPlatformSlug(CreatePlatformRequest $request)
    {
        return (new CheckPlatformSlugInteractor($this->platformRepository, $this->logger))->execute(new CheckPlatformSlugRequest([
            'slug' => $request->slug,
        ]));
    }

    /**
     * @param $errorCode
     * @return CreatePlatformResponse
     */
    private function createErrorResponse($errorCode)
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
    private function createSuccessResponse($platformID)
    {
        return new CreatePlatformResponse([
            'success' => true,
            'platformID' => $platformID
        ]);
    }
}