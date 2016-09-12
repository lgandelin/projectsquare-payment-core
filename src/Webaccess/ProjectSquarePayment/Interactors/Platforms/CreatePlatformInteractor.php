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

        $this->platformRepository->persist($platform);

        $response = new CreatePlatformResponse();
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
}