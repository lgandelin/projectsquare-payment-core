<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Signup;

use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Contracts\RemoteInfrastructureService;
use Webaccess\ProjectSquarePayment\Interactors\Administrators\CreateAdministratorInteractor;
use Webaccess\ProjectSquarePayment\Interactors\Infrastructure\CreateInfrastructureInteractor;
use Webaccess\ProjectSquarePayment\Interactors\Platforms\CreatePlatformInteractor;
use Webaccess\ProjectSquarePayment\Repositories\AdministratorRepository;
use Webaccess\ProjectSquarePayment\Repositories\NodeRepository;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Administrators\CreateAdministratorRequest;
use Webaccess\ProjectSquarePayment\Requests\Infrastructure\CreateInfrastructureRequest;
use Webaccess\ProjectSquarePayment\Requests\Platforms\CreatePlatformRequest;
use Webaccess\ProjectSquarePayment\Requests\Signup\SignupRequest;
use Webaccess\ProjectSquarePayment\Responses\Administrators\CreateAdministratorResponse;
use Webaccess\ProjectSquarePayment\Responses\Infrastructure\CreateInfrastructureResponse;
use Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse;
use Webaccess\ProjectSquarePayment\Responses\Signup\SignupResponse;

class SignupInteractor
{
    private $platformRepository;
    private $administratorRepository;
    private $nodeRepository;
    private $remoteInfrastructureService;
    private $logger;

    /**
     * @param PlatformRepository $platformRepository
     * @param AdministratorRepository $administratorRepository
     * @param NodeRepository $nodeRepository
     * @param RemoteInfrastructureService $remoteInfrastructureService
     * @param Logger $logger
     */
    public function __construct(PlatformRepository $platformRepository, AdministratorRepository $administratorRepository, NodeRepository $nodeRepository, RemoteInfrastructureService $remoteInfrastructureService, Logger $logger)
    {
        $this->platformRepository = $platformRepository;
        $this->nodeRepository = $nodeRepository;
        $this->administratorRepository = $administratorRepository;
        $this->remoteInfrastructureService = $remoteInfrastructureService;
        $this->logger = $logger;
    }

    /**
     * @param SignupRequest $request
     * @return SignupResponse
     */
    public function execute(SignupRequest $request)
    {
        $this->logger->logRequest(self::class, $request);

        $responsePlatform = $this->createPlatform($request);

        if (!$responsePlatform->success)
            return $this->createErrorResponse($responsePlatform->errorCode);

        $responseAdministrator = $this->createAdministrator($request, $responsePlatform->platformID);

        if (!$responseAdministrator->success) {
            $this->platformRepository->deleteByID($responsePlatform->platformID);
            return $this->createErrorResponse($responseAdministrator->errorCode);
        }

        $this->createInfrastructure($request, $responsePlatform->platformID);

        $response = $this->createSuccessResponse($responsePlatform->platformID, $responseAdministrator->administratorID);

        $this->logger->logResponse(self::class, $response);

        return $response;
    }

    /**
     * @param SignupRequest $request
     * @return CreatePlatformResponse
     */
    private function createPlatform(SignupRequest $request)
    {
        return (new CreatePlatformInteractor($this->platformRepository, $this->logger))->execute(new CreatePlatformRequest([
            //'name' => $request->platformName,
            'slug' => $request->platformSlug,
            'usersCount' => $request->platformUsersCount,
            'platformMonthlyCost' => $request->platformPlatformMonthlyCost,
            'userMonthlyCost' => $request->platformUserMonthlyCost,
        ]));
    }

    /**
     * @param SignupRequest $request
     * @param $platformID
     * @return CreateAdministratorResponse
     */
    private function createAdministrator(SignupRequest $request, $platformID)
    {
        return (new CreateAdministratorInteractor($this->administratorRepository, $this->logger))->execute(new CreateAdministratorRequest([
            'email' => $request->administratorEmail,
            'password' => $request->administratorPassword,
            //'lastName' => $request->administratorLastName,
            //'firstName' => $request->administratorFirstName,
            //'billingAddress' => $request->administratorBillingAddress,
            //'zipcode' => $request->administratorZipcode,
            //'city' => $request->administratorCity,
            'platformID' => $platformID
        ]));
    }

    /**
     * @param SignupRequest $request
     * @param $platformID
     * @return CreateInfrastructureResponse
     */
    private function createInfrastructure(SignupRequest $request, $platformID)
    {
        return (new CreateInfrastructureInteractor($this->nodeRepository, $this->platformRepository, $this->remoteInfrastructureService, $this->logger))->execute(new CreateInfrastructureRequest([
            'platformID' => $platformID,
            'slug' => $request->platformSlug,
            'administratorEmail' => $request->administratorEmail,
            'usersLimit' => $request->platformUsersCount,
        ]));
    }

    /**
     * @param $errorCode
     * @return SignupResponse
     */
    private function createErrorResponse($errorCode)
    {
        return new SignupResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }

    /**
     * @param $platformID
     * @param $administratorID
     * @return SignupResponse
     */
    private function createSuccessResponse($platformID, $administratorID)
    {
        return new SignupResponse([
            'success' => true,
            'platformID' => $platformID,
            'administratorID' => $administratorID
        ]);
    }
}