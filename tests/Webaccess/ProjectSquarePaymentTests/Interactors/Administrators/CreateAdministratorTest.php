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
        $response = $this->interactor->execute(new CreateAdministratorRequest([
            'email' => 'lgandelin@web-access.fr',
            'password' => '111aaa',
            'lastName' => 'Gandelin',
            'firstName' => 'Louis',
            'address' => '17, rue du lac Saint André',
            'zipCode' => '73370',
            'city' => 'Le Bourget du Lac',
            'state' => 'Savoie',
            'country' => 'France',
        ]));

        $this->assertInstanceOf(CreateAdministratorResponse::class, $response);
        $this->assertEquals(1, sizeof($this->administratorRepository->objects));
        $this->assertEquals('lgandelin@web-access.fr', $response->administrator->getEmail());
        $this->assertEquals('Gandelin', $response->administrator->getLastName());
        $this->assertEquals('Louis', $response->administrator->getFirstName());
        $this->assertEquals('73370', $response->administrator->getZipCode());
        $this->assertEquals('Savoie', $response->administrator->getState());
        $this->assertEquals('France', $response->administrator->getCountry());
        $this->assertTrue($response->success);
    }

    public function testCreateAdministratorWithoutEmail()
    {
        $response = $this->interactor->execute(new CreateAdministratorRequest([
            'password' => '111aaa',
            'lastName' => 'Gandelin',
            'firstName' => 'Louis',
            'address' => '17, rue du lac Saint André',
            'zipCode' => '73370',
            'city' => 'Le Bourget du Lac',
            'state' => 'Savoie',
            'country' => 'France',
        ]));

        $this->assertInstanceOf(CreateAdministratorResponse::class, $response);
        $this->assertEquals(0, sizeof($this->administratorRepository->objects));
        $this->assertEquals(CreateAdministratorResponse::ADMINISTRATOR_EMAIL_REQUIRED, $response->errorCode);
        $this->assertFalse($response->success);
    }
}