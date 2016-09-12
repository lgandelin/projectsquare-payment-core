<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Administrators;

use Webaccess\ProjectSquarePayment\Entities\Administrator;
use Webaccess\ProjectSquarePayment\Repositories\AdministratorRepository;
use Webaccess\ProjectSquarePayment\Requests\Administrators\CreateAdministratorRequest;
use Webaccess\ProjectSquarePayment\Responses\Administrators\CreateAdministratorResponse;

class CreateAdministratorInteractor
{
    private $administratorRepository;

    /**
     * @param AdministratorRepository $administratorRepository
     */
    public function __construct(AdministratorRepository $administratorRepository)
    {
        $this->administratorRepository = $administratorRepository;
    }

    /**
     * @param CreateAdministratorRequest $request
     * @return CreateAdministratorResponse
     */
    public function execute(CreateAdministratorRequest $request): CreateAdministratorResponse
    {
        $errorCode = null;
        $administrator = $this->createObjectFromRequest($request);

        /*if (!$administrator->getName())
            $errorCode = CreateAdministratorResponse::ADMINISTRATOR_NAME_REQUIRED;*/

        if (!$this->administratorRepository->persist($administrator))
            $errorCode = CreateAdministratorResponse::REPOSITORY_INSERTION_FAILED;

        return ($errorCode === null) ? $this->createSuccessResponse($administrator) : $this->createErrorResponse($errorCode);
    }

    /**
     * @param CreateAdministratorRequest $request
     * @return Administrator
     */
    private function createObjectFromRequest(CreateAdministratorRequest $request): Administrator
    {
        $administrator = new Administrator();
        $administrator->setEmail($request->email);
        $administrator->setPassword($request->password);
        $administrator->setLastName($request->lastName);
        $administrator->setFirstName($request->firstName);
        $administrator->setAddress($request->address);
        $administrator->setZipCode($request->zipCode);
        $administrator->setCity($request->city);
        $administrator->setState($request->state);
        $administrator->setCountry($request->country);

        return $administrator;
    }

    /**
     * @param $errorCode
     * @return CreateAdministratorResponse
     */
    private function createErrorResponse($errorCode): CreateAdministratorResponse
    {
        $response = new CreateAdministratorResponse();
        $response->success = false;
        $response->errorCode = $errorCode;

        return $response;
    }

    /**
     * @param $administrator
     * @return CreateAdministratorResponse
     */
    private function createSuccessResponse(Administrator $administrator): CreateAdministratorResponse
    {
        $response = new CreateAdministratorResponse();
        $response->success = true;
        $response->administrator = $administrator;

        return $response;
    }
}