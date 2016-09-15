<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Signup;

use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Signup\CheckPlatformSlugRequest;
use Webaccess\ProjectSquarePayment\Responses\Signup\CheckPlatformSlugResponse;

class CheckPlatformSlugInteractor
{
    private $platformRepository;

    /**
     * @param PlatformRepository $platformRepository
     */
    public function __construct(PlatformRepository $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    public function execute(CheckPlatformSlugRequest $request): CheckPlatformSlugResponse
    {
        $errorCode = null;

        if (!$this->isSlugValid($request->slug))
            $errorCode = CheckPlatformSlugResponse::PLATFORM_SLUG_INVALID;

        elseif (!$this->isSlugAvailable($request->slug))
            $errorCode = CheckPlatformSlugResponse::PLATFORM_SLUG_UNAVAILABLE;

        return ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);
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
     * @return CheckPlatformSlugResponse
     */
    private function createErrorResponse($errorCode): CheckPlatformSlugResponse
    {
        return new CheckPlatformSlugResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }

    /**
     * @return CheckPlatformSlugResponse
     */
    private function createSuccessResponse(): CheckPlatformSlugResponse
    {
        return new CheckPlatformSlugResponse([
            'success' => true
        ]);
    }
}