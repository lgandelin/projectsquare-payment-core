<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Signup;

use Webaccess\ProjectSquarePayment\Interactors\Administrators\CreateAdministratorInteractor;
use Webaccess\ProjectSquarePayment\Interactors\Platforms\CreatePlatformInteractor;
use Webaccess\ProjectSquarePayment\Repositories\AdministratorRepository;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Administrators\CreateAdministratorRequest;
use Webaccess\ProjectSquarePayment\Requests\Platforms\CreatePlatformRequest;
use Webaccess\ProjectSquarePayment\Requests\Signup\SignupRequest;
use Webaccess\ProjectSquarePayment\Responses\Administrators\CreateAdministratorResponse;
use Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse;
use Webaccess\ProjectSquarePayment\Responses\Signup\SignupResponse;
use Webaccess\ProjectSquarePayment\Services\RemoteInfrastructureGenerator;

class SignupInteractor
{
    private $platformRepository;
    private $administratorRepository;

    /**
     * @param PlatformRepository $platformRepository
     * @param AdministratorRepository $administratorRepository
     */
    public function __construct(PlatformRepository $platformRepository, AdministratorRepository $administratorRepository)
    {
        $this->platformRepository = $platformRepository;
        $this->administratorRepository = $administratorRepository;
    }

    /**
     * @param SignupRequest $request
     * @return SignupResponse
     */
    public function execute(SignupRequest $request)
    {
        $responsePlatform = $this->createPlatform($request);

        if (!$responsePlatform->success)
            return $this->createErrorResponse($responsePlatform->errorCode);

        $responseAdministrator = $this->createAdministrator($request, $responsePlatform->platformID);

        if (!$responseAdministrator->success) {
            $this->platformRepository->deleteByID($responsePlatform->platformID);
            return $this->createErrorResponse($responseAdministrator->errorCode);
        }

        $this->createRemoteInfrastructure($request, $responsePlatform->platformID);

        return $this->createSuccessResponse($responsePlatform->platformID, $responseAdministrator->administratorID);
    }

    /**
     * @param SignupRequest $request
     * @return CreatePlatformResponse
     */
    private function createPlatform(SignupRequest $request)
    {
        return (new CreatePlatformInteractor($this->platformRepository))->execute(new CreatePlatformRequest([
            'name' => $request->platformName,
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
        return (new CreateAdministratorInteractor($this->administratorRepository))->execute(new CreateAdministratorRequest([
            'email' => $request->administratorEmail,
            'password' => $request->administratorPassword,
            'lastName' => $request->administratorLastName,
            'firstName' => $request->administratorFirstName,
            'billingAddress' => $request->administratorBillingAddress,
            'zipcode' => $request->administratorZipcode,
            'city' => $request->administratorCity,
            'platformID' => $platformID
        ]));
    }

    /**
     * @param SignupRequest $request
     * @param $platformID
     */
    private function createRemoteInfrastructure(SignupRequest $request, $platformID)
    {
        /*return (new CreateRemoteInfrastructureInteractor())->execute(new CreateRemoteInfrastructureRequest([
            'slug' => $request->platformSlug,
            'administratorEmail' => $request->administratorEmail,
            'usersLimit' => $request->platformUsersCount,
        ]));*/
        //$this->remoteInfrastructureGenerator->launchEnvCreation($request->platformSlug, $request->administratorEmail, $request->platformUsersCount, $nodeIdentifier);
        /*$nodeIdentifier = $this->nodeRepository->getAvailableNodeIdentifier();

        if (!$nodeIdentifier) {
            $nodeIdentifier = $this->nodeRepository->persistNewNode();
            DigitalOceanService::launchEnvCreation($nodeIdentifier, $request->platformSlug, $request->administratorEmail, $request->usersCount);
        } else {
            DigitalOceanService::launchAppCreation($nodeIdentifier, $request->platformSlug, $request->administratorEmail, $request->usersCount);
            $this->nodeRepository->setNodeUnavailable($nodeIdentifier);
        }

        $this->nodeRepository->updatePlatformNodeID($platformID, $nodeIdentifier);

        $nodeIdentifier = $this->nodeRepository->persistNewNode();
        DigitalOceanService::launchNodeCreation($nodeIdentifier);*/
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