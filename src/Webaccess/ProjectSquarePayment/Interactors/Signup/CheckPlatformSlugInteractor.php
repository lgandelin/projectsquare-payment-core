<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Signup;

use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Signup\CheckPlatformSlugRequest;
use Webaccess\ProjectSquarePayment\Responses\Signup\CheckPlatformSlugResponse;

class CheckPlatformSlugInteractor
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
     * @param CheckPlatformSlugRequest $request
     * @return CheckPlatformSlugResponse
     */
    public function execute(CheckPlatformSlugRequest $request)
    {
        //$this->logger->logRequest(self::class, $request);

        $errorCode = null;

        if (!$this->isSlugValid($request->slug))
            $errorCode = CheckPlatformSlugResponse::PLATFORM_SLUG_INVALID;

        elseif (!$this->isSlugAvailable($request->slug))
            $errorCode = CheckPlatformSlugResponse::PLATFORM_SLUG_UNAVAILABLE;

        $response = ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);

        //$this->logger->logResponse(self::class, $response);

        return $response;
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
    private function createErrorResponse($errorCode)
    {
        return new CheckPlatformSlugResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }

    /**
     * @return CheckPlatformSlugResponse
     */
    private function createSuccessResponse()
    {
        return new CheckPlatformSlugResponse([
            'success' => true
        ]);
    }
}