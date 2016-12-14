<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Administrators;

use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Repositories\AdministratorRepository;
use Webaccess\ProjectSquarePayment\Requests\Administrators\UpdateAdministratorRequest;
use Webaccess\ProjectSquarePayment\Responses\Administrators\UpdateAdministratorResponse;

class UpdateAdministratorInteractor
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
     * @param UpdateAdministratorRequest $request
     * @return UpdateAdministratorResponse
     */
    public function execute(UpdateAdministratorRequest $request)
    {
        $this->logger->logRequest(self::class, $request);

        $errorCode = null;

        if (!$administrator = $this->administratorRepository->getByID($request->administratorID))
            $errorCode = UpdateAdministratorResponse::ADMINISTRATOR_NOT_FOUND_ERROR;

        elseif ($request->email !== null && $request->email == '')
            $errorCode = UpdateAdministratorResponse::ADMINISTRATOR_EMAIL_REQUIRED;

        elseif (!$request->lastName !== null && $request->lastName == '')
            $errorCode = UpdateAdministratorResponse::ADMINISTRATOR_LAST_NAME_REQUIRED;

        elseif (!$request->firstName !== null && $request->firstName == '')
            $errorCode = UpdateAdministratorResponse::ADMINISTRATOR_FIRST_NAME_REQUIRED;

        elseif (!$request->city !== null && $request->city == '')
            $errorCode = UpdateAdministratorResponse::ADMINISTRATOR_CITY_REQUIRED;

        elseif (!$request->billingAddress !== null && $request->billingAddress == '')
            $errorCode = UpdateAdministratorResponse::ADMINISTRATOR_BILLING_ADDRESS_REQUIRED;

        elseif (!$request->zipcode !== null && $request->zipcode == '')
            $errorCode = UpdateAdministratorResponse::ADMINISTRATOR_ZIPCODE_REQUIRED;

        elseif (!$this->updateAdministrator($request, $administrator))
            $errorCode = UpdateAdministratorResponse::REPOSITORY_CREATION_FAILED;


        $response = ($errorCode === null) ? $this->createSuccessResponse() : $this->createErrorResponse($errorCode);

        $this->logger->logResponse(self::class, $response);

        return $response;
    }

    /**
     * @param UpdateAdministratorRequest $request
     * @param $administrator
     */
    private function updateAdministrator(UpdateAdministratorRequest $request, $administrator)
    {
        if ($request->email !== null && $request->email !== $administrator->getEmail()) $administrator->setEmail($request->email);
        if ($request->password !== null && $request->password !== $administrator->getPassword()) $administrator->setPassword($request->password);
        if ($request->firstName !== null && $request->firstName !== $administrator->getFirstName()) $administrator->setFirstName($request->firstName);
        if ($request->lastName !== null && $request->lastName !== $administrator->getLastName()) $administrator->setLastName($request->lastName);
        if ($request->billingAddress !== null && $request->billingAddress !== $administrator->getBillingAddress()) $administrator->setBillingAddress($request->billingAddress);
        if ($request->city !== null && $request->city !== $administrator->getCity()) $administrator->setBillingAddress($request->city);
        if ($request->zipcode !== null && $request->zipcode !== $administrator->getZipCode()) $administrator->setZipcode($request->zipcode);

        return $this->administratorRepository->persist($administrator);
    }

    /**
     * @param $errorCode
     * @return UpdateAdministratorResponse
     */
    private function createErrorResponse($errorCode)
    {
        return new UpdateAdministratorResponse([
            'success' => false,
            'errorCode' => $errorCode
        ]);
    }

    /**
     * @return UpdateAdministratorResponse
     */
    private function createSuccessResponse()
    {
        return new UpdateAdministratorResponse([
            'success' => true,
        ]);
    }
}