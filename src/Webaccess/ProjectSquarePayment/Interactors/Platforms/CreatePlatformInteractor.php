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
        $platform = $this->createObjectFromRequest($request);
        $response = new CreatePlatformResponse();

        if (!$platform->getName())
            return $this->createResponseWithErrorCode($response, CreatePlatformResponse::PLATFORM_NAME_REQUIRED_ERROR_CODE);

        if (!$this->platformRepository->persist($platform))
            return $this->createResponseWithErrorCode($response, CreatePlatformResponse::REPOSITORY_INSERTION_FAILED_ERROR_CODE);

        $response->success = true;
        $response->platform = $platform;

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

        return $platform;
    }

    /**
     * @param CreatePlatformResponse $response
     * @param $errorCode
     * @return CreatePlatformResponse
     */
    private function createResponseWithErrorCode(CreatePlatformResponse $response, $errorCode)
    {
        $response->success = false;
        $response->errorCode = $errorCode;

        return $response;
    }
}