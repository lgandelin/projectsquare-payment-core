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

        if (!$administrator->getEmail())
            $errorCode = CreateAdministratorResponse::ADMINISTRATOR_EMAIL_REQUIRED;

        elseif (!$administrator->getPassword())
            $errorCode = CreateAdministratorResponse::ADMINISTRATOR_PASSWORD_REQUIRED;

        elseif (!$administrator->getLastName())
            $errorCode = CreateAdministratorResponse::ADMINISTRATOR_LAST_NAME_REQUIRED;

        elseif (!$administrator->getFirstName())
            $errorCode = CreateAdministratorResponse::ADMINISTRATOR_FIRST_NAME_REQUIRED;

        elseif (!$administrator->getPlatformID())
            $errorCode = CreateAdministratorResponse::PLATFORM_ID_REQUIRED;

        elseif (!$administratorID = $this->administratorRepository->persist($administrator))
            $errorCode = CreateAdministratorResponse::REPOSITORY_CREATION_FAILED;

        return ($errorCode === null) ? $this->createSuccessResponse($administratorID) : $this->createErrorResponse($errorCode);
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
        $administrator->setBillingAddress($request->billingAddress);
        $administrator->setZipcode($request->zipcode);
        $administrator->setCity($request->city);
        $administrator->setPlatformID($request->platformID);

        return $administrator;
    }

    /**
     * @param $errorCode
     * @return CreateAdministratorResponse
     */
    private function createErrorResponse($errorCode): CreateAdministratorResponse
    {
        return new CreateAdministratorResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }

    /**
     * @param $administratorID
     * @return CreateAdministratorResponse
     */
    private function createSuccessResponse($administratorID): CreateAdministratorResponse
    {
        return new CreateAdministratorResponse([
            'success' => true,
            'administratorID' => $administratorID
        ]);
    }
}