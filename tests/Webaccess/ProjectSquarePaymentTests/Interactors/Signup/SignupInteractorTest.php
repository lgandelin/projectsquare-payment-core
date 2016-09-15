<?php

use Webaccess\ProjectSquarePayment\Interactors\Signup\SignupInteractor;
use Webaccess\ProjectSquarePayment\Requests\Signup\SignupRequest;
use Webaccess\ProjectSquarePayment\Responses\Administrators\CreateAdministratorResponse;
use Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse;
use Webaccess\ProjectSquarePayment\Responses\Signup\SignupResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class SignupInteractorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new SignupInteractor($this->platformRepository, $this->administratorRepository);
    }

    public function testSignup()
    {
        $response = $this->interactor->execute(new SignupRequest([
            'platformName' => 'Webaccess',
            'platformSlug' => 'webaccess',
            'platformUsersCount' => 3,
            'administratorEmail' => 'lgandelin@web-access.fr',
            'administratorPassword' => '111aaa',
            'administratorLastName' => 'Gandelin',
            'administratorFirstName' => 'Louis',
            'administratorBillingAddress' => '17, rue du lac Saint André',
            'administratorZipcode' => '73370',
            'administratorCity' => 'Le Bourget du Lac',
        ]));

        $this->assertInstanceOf(SignupResponse::class, $response);
        $this->assertEquals(1, sizeof($this->platformRepository->objects));
        $this->assertEquals(1, sizeof($this->administratorRepository->objects));
        $platform = $this->platformRepository->getByID($response->platformID);
        $this->assertEquals('Webaccess', $platform->getName());
        $this->assertEquals('webaccess', $platform->getSlug());
        $this->assertEquals(3, $platform->getUsersCount());
        $this->assertEquals(new DateTime(), $platform->getCreationDate());
        $administrator = $this->administratorRepository->getByID($response->administratorID);
        $this->assertEquals('lgandelin@web-access.fr', $administrator->getEmail());
        $this->assertEquals('Gandelin', $administrator->getLastName());
        $this->assertEquals('Louis', $administrator->getFirstName());
        $this->assertEquals('73370', $administrator->getZipCode());
        $this->assertTrue($response->success);
    }

    public function testSignupWithErrorWithPlatform()
    {
        $response = $this->interactor->execute(new SignupRequest([
            'platformName' => 'Webaccess',
            'platformUsersCount' => 3,
            'administratorEmail' => 'lgandelin@web-access.fr',
            'administratorPassword' => '111aaa',
            'administratorLastName' => 'Gandelin',
            'administratorFirstName' => 'Louis',
            'administratorBillingAddress' => '17, rue du lac Saint André',
            'administratorZipcode' => '73370',
            'administratorCity' => 'Le Bourget du Lac',
        ]));

        $this->assertInstanceOf(SignupResponse::class, $response);
        $this->assertEquals(0, sizeof($this->platformRepository->objects));
        $this->assertEquals(0, sizeof($this->administratorRepository->objects));
        $this->assertEquals(CreatePlatformResponse::PLATFORM_SLUG_REQUIRED, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testSignupWithErrorWithAdministrator()
    {
        $response = $this->interactor->execute(new SignupRequest([
            'platformName' => 'Webaccess',
            'platformSlug' => 'webaccess',
            'platformUsersCount' => 3,
            'administratorPassword' => '111aaa',
            'administratorLastName' => 'Gandelin',
            'administratorFirstName' => 'Louis',
            'administratorBillingAddress' => '17, rue du lac Saint André',
            'administratorZipcode' => '73370',
            'administratorCity' => 'Le Bourget du Lac',
        ]));

        $this->assertInstanceOf(SignupResponse::class, $response);
        $this->assertEquals(0, sizeof($this->platformRepository->objects));
        $this->assertEquals(0, sizeof($this->administratorRepository->objects));
        $this->assertEquals(CreateAdministratorResponse::ADMINISTRATOR_EMAIL_REQUIRED, $response->errorCode);
        $this->assertFalse($response->success);
    }
}