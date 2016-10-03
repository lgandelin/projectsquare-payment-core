<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Signup;

use Webaccess\ProjectSquarePayment\Interactors\Administrators\CreateAdministratorInteractor;
use Webaccess\ProjectSquarePayment\Interactors\Platforms\CreatePlatformInteractor;
use Webaccess\ProjectSquarePayment\Repositories\AdministratorRepository;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Administrators\CreateAdministratorRequest;
use Webaccess\ProjectSquarePayment\Requests\Platforms\CreatePlatformRequest;
use Webaccess\ProjectSquarePayment\Requests\Signup\SignupRequest;
use Webaccess\ProjectSquarePayment\Responses\Signup\SignupResponse;

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

        return $this->createSuccessResponse($responsePlatform->platformID, $responseAdministrator->administratorID);
    }

    /**
     * @param SignupRequest $request
     * @return \Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse
     */
    private function createPlatform(SignupRequest $request)
    {
        $responsePlatform = (new CreatePlatformInteractor($this->platformRepository))->execute(new CreatePlatformRequest([
            'name' => $request->platformName,
            'slug' => $request->platformSlug,
            'usersCount' => $request->platformUsersCount,
        ]));
        return $responsePlatform;
    }

    /**
     * @param SignupRequest $request
     * @param $platformID
     * @return \Webaccess\ProjectSquarePayment\Responses\Administrators\CreateAdministratorResponse
     */
    private function createAdministrator(SignupRequest $request, $platformID)
    {
        $responseAdministrator = (new CreateAdministratorInteractor($this->administratorRepository))->execute(new CreateAdministratorRequest([
            'email' => $request->administratorEmail,
            'password' => $request->administratorPassword,
            'lastName' => $request->administratorLastName,
            'firstName' => $request->administratorFirstName,
            'billingAddress' => $request->administratorBillingAddress,
            'zipcode' => $request->administratorZipcode,
            'city' => $request->administratorCity,
            'platformID' => $platformID
        ]));

        return $responseAdministrator;
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