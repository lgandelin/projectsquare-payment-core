<?php


use Webaccess\ProjectSquarePayment\Interactors\Administrators\CreateAdministratorInteractor;
use Webaccess\ProjectSquarePayment\Requests\Administrators\CreateAdministratorRequest;
use Webaccess\ProjectSquarePayment\Responses\Administrators\CreateAdministratorResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class CreateAdministratorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateAdministratorInteractor($this->administratorRepository);
    }

    public function testCreateAdministrator()
    {
        $platform = $this->createSamplePlatform();
        $response = $this->interactor->execute(new CreateAdministratorRequest([
            'email' => 'lgandelin@web-access.fr',
            'password' => '111aaa',
            'lastName' => 'Gandelin',
            'firstName' => 'Louis',
            'billingAddress' => '17, rue du lac Saint André',
            'zipcode' => '73370',
            'city' => 'Le Bourget du Lac',
            'platformID' => $platform->getId(),
        ]));

        $this->assertInstanceOf(CreateAdministratorResponse::class, $response);
        $this->assertEquals(1, sizeof($this->administratorRepository->objects));
        $administrator = $this->administratorRepository->getByID($response->administratorID);
        $this->assertEquals('lgandelin@web-access.fr', $administrator->getEmail());
        $this->assertEquals('Gandelin', $administrator->getLastName());
        $this->assertEquals('Louis', $administrator->getFirstName());
        $this->assertEquals('73370', $administrator->getZipCode());
        $this->assertTrue($response->success);
    }

    public function testCreateAdministratorWithoutEmail()
    {
        $platform = $this->createSamplePlatform();
        $response = $this->interactor->execute(new CreateAdministratorRequest([
            'password' => '111aaa',
            'lastName' => 'Gandelin',
            'firstName' => 'Louis',
            'billingAddress' => '17, rue du lac Saint André',
            'zipcode' => '73370',
            'city' => 'Le Bourget du Lac',
            'platformID' => $platform->getID(),
        ]));

        $this->assertInstanceOf(CreateAdministratorResponse::class, $response);
        $this->assertEquals(0, sizeof($this->administratorRepository->objects));
        $this->assertEquals(CreateAdministratorResponse::ADMINISTRATOR_EMAIL_REQUIRED, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testCreateAdministratorWithoutPlatformID()
    {
        $response = $this->interactor->execute(new CreateAdministratorRequest([
            'email' => 'lgandelin@web-access.fr',
            'password' => '111aaa',
            'lastName' => 'Gandelin',
            'firstName' => 'Louis',
            'billingAddress' => '17, rue du lac Saint André',
            'zipcode' => '73370',
            'city' => 'Le Bourget du Lac',
        ]));

        $this->assertInstanceOf(CreateAdministratorResponse::class, $response);
        $this->assertEquals(0, sizeof($this->administratorRepository->objects));
        $this->assertEquals(CreateAdministratorResponse::PLATFORM_ID_REQUIRED, $response->errorCode);
        $this->assertFalse($response->success);
    }
}