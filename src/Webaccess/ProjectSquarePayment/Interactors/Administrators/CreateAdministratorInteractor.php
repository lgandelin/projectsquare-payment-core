<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Administrators;

use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Entities\Administrator;
use Webaccess\ProjectSquarePayment\Repositories\AdministratorRepository;
use Webaccess\ProjectSquarePayment\Requests\Administrators\CreateAdministratorRequest;
use Webaccess\ProjectSquarePayment\Responses\Administrators\CreateAdministratorResponse;

class CreateAdministratorInteractor
{
    private $administratorRepository;
    private $logger;

    /**
     * @param AdministratorRepository $administratorRepository
     * @param Logger $logger
     */
    public function __construct(AdministratorRepository $administratorRepository, Logger $logger)
    {
        $this->administratorRepository = $administratorRepository;
        $this->logger = $logger;
    }

    /**
     * @param CreateAdministratorRequest $request
     * @return CreateAdministratorResponse
     */
    public function execute(CreateAdministratorRequest $request)
    {
        $this->logger->logRequest(self::class, $request);

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

        elseif (!$administrator->getCity())
            $errorCode = CreateAdministratorResponse::ADMINISTRATOR_CITY_REQUIRED;

        elseif (!$administrator->getBillingAddress())
            $errorCode = CreateAdministratorResponse::ADMINISTRATOR_BILLING_ADDRESS_REQUIRED;

        elseif (!$administrator->getZipCode())
            $errorCode = CreateAdministratorResponse::ADMINISTRATOR_ZIPCODE_REQUIRED;

        elseif (!$administrator->getPlatformID())
            $errorCode = CreateAdministratorResponse::PLATFORM_ID_REQUIRED;

        elseif (!$administratorID = $this->administratorRepository->persist($administrator))
            $errorCode = CreateAdministratorResponse::REPOSITORY_CREATION_FAILED;

        $response = ($errorCode === null) ? $this->createSuccessResponse($administratorID) : $this->createErrorResponse($errorCode);

        $this->logger->logResponse(self::class, $response);

        return $response;
    }

    /**
     * @param CreateAdministratorRequest $request
     * @return Administrator
     */
    private function createObjectFromRequest(CreateAdministratorRequest $request)
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
    private function createErrorResponse($errorCode)
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
    private function createSuccessResponse($administratorID)
    {
        return new CreateAdministratorResponse([
            'success' => true,
            'administratorID' => $administratorID
        ]);
    }
}