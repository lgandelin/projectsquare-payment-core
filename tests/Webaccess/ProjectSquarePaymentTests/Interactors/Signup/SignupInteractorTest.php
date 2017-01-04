<?php

use Webaccess\ProjectSquarePayment\Contracts\RemoteInfrastructureService;
use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Interactors\Signup\SignupInteractor;
use Webaccess\ProjectSquarePayment\Requests\Signup\SignupRequest;
use Webaccess\ProjectSquarePayment\Responses\Administrators\CreateAdministratorResponse;
use Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse;
use Webaccess\ProjectSquarePayment\Responses\Signup\CheckPlatformSlugResponse;
use Webaccess\ProjectSquarePayment\Responses\Signup\SignupResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class SignupInteractorTest extends ProjectsquareTestCase
{

    public function testSignup()
    {
        $remoteInfrastructureServiceMock = Mockery::mock(RemoteInfrastructureService::class)
            ->shouldReceive('launchEnvCreation')->once()->with(Mockery::type('string'), 'webaccess', 'lgandelin@web-access.fr', 3)
            ->shouldReceive('launchNodeCreation')->once()
            ->shouldReceive('launchAppCreation')->never()
            ->mock();

        $interactor = new SignupInteractor($this->platformRepository, $this->administratorRepository, $this->nodeRepository, $remoteInfrastructureServiceMock, $this->getLoggerMock());

        $response = $interactor->execute(new SignupRequest([
            //'platformName' => 'Webaccess',
            'platformSlug' => 'webaccess',
            'platformUsersCount' => 3,
            'platformPlatformMonthlyCost' => 20,
            'platformUserMonthlyCost' => 10,
            'administratorEmail' => 'lgandelin@web-access.fr',
            'administratorPassword' => '111aaa',
            /*'administratorLastName' => 'Gandelin',
            'administratorFirstName' => 'Louis',
            'administratorBillingAddress' => '17, rue du lac Saint André',
            'administratorZipcode' => '73370',
            'administratorCity' => 'Le Bourget du Lac',*/
        ]));

        $this->assertInstanceOf(SignupResponse::class, $response);
        $this->assertEquals(1, sizeof($this->platformRepository->objects));
        $this->assertEquals(1, sizeof($this->administratorRepository->objects));
        $platform = $this->platformRepository->getByID($response->platformID);
        //$this->assertEquals('Webaccess', $platform->getName());
        $this->assertEquals('webaccess', $platform->getSlug());
        $this->assertEquals(3, $platform->getUsersCount());
        $this->assertEquals(Platform::PLATFORM_STATUS_TRIAL_PERIOD, $platform->getStatus());
        $this->assertEquals(20, $platform->getPlatformMonthlyCost());
        $this->assertEquals(10, $platform->getUserMonthlyCost());
        $this->assertEquals(new DateTime(), $platform->getCreationDate());
        $administrator = $this->administratorRepository->getByID($response->administratorID);
        $this->assertEquals('lgandelin@web-access.fr', $administrator->getEmail());
        /*$this->assertEquals('Gandelin', $administrator->getLastName());
        $this->assertEquals('Louis', $administrator->getFirstName());
        $this->assertEquals('17, rue du lac Saint André', $administrator->getBillingAddress());
        $this->assertEquals('73370', $administrator->getZipCode());
        $this->assertEquals('Le Bourget du Lac', $administrator->getCity());*/
        $this->assertTrue($response->success);
    }

    public function testSignupWithErrorWithPlatform()
    {
        $remoteInfrastructureGeneratorMock = Mockery::mock(RemoteInfrastructureService::class)
            ->shouldReceive('launchEnvCreation')->never()
            ->shouldReceive('launchNodeCreation')->never()
            ->shouldReceive('launchAppCreation')->never()
            ->mock();

        $interactor = new SignupInteractor($this->platformRepository, $this->administratorRepository, $this->nodeRepository, $remoteInfrastructureGeneratorMock, $this->getLoggerMock());

        $response = $interactor->execute(new SignupRequest([
            //'platformName' => 'Webaccess',
            'platformUsersCount' => 3,
            'administratorEmail' => 'lgandelin@web-access.fr',
            'administratorPassword' => '111aaa',
            /*'administratorLastName' => 'Gandelin',
            'administratorFirstName' => 'Louis',
            'administratorBillingAddress' => '17, rue du lac Saint André',
            'administratorZipcode' => '73370',
            'administratorCity' => 'Le Bourget du Lac',*/
        ]));

        $this->assertInstanceOf(SignupResponse::class, $response);
        $this->assertEquals(0, sizeof($this->platformRepository->objects));
        $this->assertEquals(0, sizeof($this->administratorRepository->objects));
        $this->assertEquals(CreatePlatformResponse::PLATFORM_SLUG_REQUIRED, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testSignupWithInvalidPlatformSlug()
    {
        $remoteInfrastructureGeneratorMock = Mockery::mock(RemoteInfrastructureService::class)
            ->shouldReceive('launchEnvCreation')->never()
            ->shouldReceive('launchNodeCreation')->never()
            ->shouldReceive('launchAppCreation')->never()
            ->mock();

        $interactor = new SignupInteractor($this->platformRepository, $this->administratorRepository, $this->nodeRepository, $remoteInfrastructureGeneratorMock, $this->getLoggerMock());

        $response = $interactor->execute(new SignupRequest([
            //'platformName' => 'Webaccess',
            'platformSlug' => 'web@ccess',
            'platformUsersCount' => 3,
            'administratorEmail' => 'lgandelin@web-access.fr',
            'administratorPassword' => '111aaa',
            /*'administratorLastName' => 'Gandelin',
            'administratorFirstName' => 'Louis',
            'administratorBillingAddress' => '17, rue du lac Saint André',
            'administratorZipcode' => '73370',
            'administratorCity' => 'Le Bourget du Lac',*/
        ]));

        $this->assertInstanceOf(SignupResponse::class, $response);
        $this->assertEquals(0, sizeof($this->platformRepository->objects));
        $this->assertEquals(0, sizeof($this->administratorRepository->objects));
        $this->assertEquals(CheckPlatformSlugResponse::PLATFORM_SLUG_INVALID, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testSignupWithErrorWithAdministrator()
    {
        $remoteInfrastructureGeneratorMock = Mockery::mock(RemoteInfrastructureService::class)
            ->shouldReceive('launchEnvCreation')->never()
            ->shouldReceive('launchNodeCreation')->never()
            ->shouldReceive('launchAppCreation')->never()
            ->mock();

        $interactor = new SignupInteractor($this->platformRepository, $this->administratorRepository, $this->nodeRepository, $remoteInfrastructureGeneratorMock, $this->getLoggerMock());

        $response = $interactor->execute(new SignupRequest([
            //'platformName' => 'Webaccess',
            'platformSlug' => 'webaccess',
            'platformUsersCount' => 3,
            'administratorPassword' => '111aaa',
            /*'administratorLastName' => 'Gandelin',
            'administratorFirstName' => 'Louis',
            'administratorBillingAddress' => '17, rue du lac Saint André',
            'administratorZipcode' => '73370',
            'administratorCity' => 'Le Bourget du Lac',*/
        ]));

        $this->assertInstanceOf(SignupResponse::class, $response);
        $this->assertEquals(0, sizeof($this->platformRepository->objects));
        $this->assertEquals(0, sizeof($this->administratorRepository->objects));
        $this->assertEquals(CreateAdministratorResponse::ADMINISTRATOR_EMAIL_REQUIRED, $response->errorCode);
        $this->assertFalse($response->success);
    }
}