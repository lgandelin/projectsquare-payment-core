<?php

use Webaccess\ProjectSquarePayment\Interactors\Administrators\UpdateAdministratorInteractor;
use Webaccess\ProjectSquarePayment\Requests\Administrators\UpdateAdministratorRequest;
use Webaccess\ProjectSquarePayment\Responses\Administrators\UpdateAdministratorResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class UpdateAdministratorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateAdministratorInteractor($this->administratorRepository, $this->getLoggerMock());
    }

    public function testUpdateAdministrator()
    {
        $platform = $this->createSamplePlatform();
        $administrator = $this->createSampleAdministrator($platform->getID());

        $response = $this->interactor->execute(new UpdateAdministratorRequest([
            'administratorID' => $administrator->getId(),
            'lastName' => 'Gandelin',
            'firstName' => 'Louis',
            'email' => 'lgandelin@web-access.fr',
            'zipcode' => '73370',
            'billingAddress' => '17, rue du lac Saint André',
            'city' => 'Le Bourget du Lac',
        ]));

        $this->assertInstanceOf(UpdateAdministratorResponse::class, $response);
        $administrator = $this->administratorRepository->getByID($administrator->getId());
        $this->assertEquals('lgandelin@web-access.fr', $administrator->getEmail());
        $this->assertEquals('Gandelin', $administrator->getLastName());
        $this->assertEquals('Louis', $administrator->getFirstName());
        $this->assertEquals('73370', $administrator->getZipCode());
        $this->assertTrue($response->success);
    }

    public function testUpdateAdministratorWithInvalidID()
    {
        $this->createSamplePlatform();

        $response = $this->interactor->execute(new UpdateAdministratorRequest([
            'administratorID' => 2,
        ]));

        $this->assertInstanceOf(UpdateAdministratorResponse::class, $response);
        $this->assertFalse($response->success);
        $this->assertEquals(UpdateAdministratorResponse::ADMINISTRATOR_NOT_FOUND_ERROR, $response->errorCode);
    }
}
