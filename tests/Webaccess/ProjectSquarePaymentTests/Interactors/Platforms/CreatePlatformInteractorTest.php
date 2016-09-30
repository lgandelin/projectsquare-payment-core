<?php

use Webaccess\ProjectSquarePayment\Interactors\Platforms\CreatePlatformInteractor;
use Webaccess\ProjectSquarePayment\Requests\Platforms\CreatePlatformRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse;
use Webaccess\ProjectSquarePayment\Responses\Signup\CheckPlatformSlugResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class CreatePlatformInteractorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreatePlatformInteractor($this->platformRepository);
    }

    public function testCreatePlatform()
    {
        $response = $this->interactor->execute(new CreatePlatformRequest([
            'name' => 'Webaccess',
            'slug' => 'webaccess',
            'usersCount' => 3
        ]));

        $this->assertInstanceOf(CreatePlatformResponse::class, $response);
        $this->assertEquals(1, sizeof($this->platformRepository->objects));
        $platform = $this->platformRepository->getByID($response->platformID);
        $this->assertEquals('Webaccess', $platform->getName());
        $this->assertEquals('webaccess', $platform->getSlug());
        $this->assertEquals(3, $platform->getUsersCount());
        $this->assertEquals(new DateTime(), $platform->getCreationDate());
        $this->assertTrue($response->success);
    }

    public function testCreatePlatformWithoutName()
    {
        $response = $this->interactor->execute(new CreatePlatformRequest([
            'slug' => 'webaccess',
            'usersCount' => 3
        ]));

        $this->assertInstanceOf(CreatePlatformResponse::class, $response);
        $this->assertEquals(0, sizeof($this->platformRepository->objects));
        $this->assertEquals(CreatePlatformResponse::PLATFORM_NAME_REQUIRED, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testCreatePlatformWithSlugUnavailable()
    {
        $this->createSamplePlatform();

        $response = $this->interactor->execute(new CreatePlatformRequest([
            'name' => 'Webaccess',
            'slug' => 'webaccess',
            'usersCount' => 3
        ]));

        $this->assertInstanceOf(CreatePlatformResponse::class, $response);
        $this->assertEquals(1, sizeof($this->platformRepository->objects));
        $this->assertEquals(CheckPlatformSlugResponse::PLATFORM_SLUG_UNAVAILABLE, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testCreatePlatformWithoutUsersCount()
    {
        $response = $this->interactor->execute(new CreatePlatformRequest([
            'name' => 'Webaccess',
            'slug' => 'webaccess',
        ]));

        $this->assertInstanceOf(CreatePlatformResponse::class, $response);
        $this->assertEquals(0, sizeof($this->platformRepository->objects));
        $this->assertEquals(CreatePlatformResponse::PLATFORM_USERS_COUNT_REQUIRED, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testCreatePlatformWithoutSlug()
    {
        $response = $this->interactor->execute(new CreatePlatformRequest([
            'name' => 'Webaccess',
            'usersCount' => 3
        ]));

        $this->assertInstanceOf(CreatePlatformResponse::class, $response);
        $this->assertEquals(0, sizeof($this->platformRepository->objects));
        $this->assertEquals(CreatePlatformResponse::PLATFORM_SLUG_REQUIRED, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testCreatePlatformWithInvalidSlug()
    {
        $response = $this->interactor->execute(new CreatePlatformRequest([
            'name' => 'Webaccess',
            'slug' => 'web.access',
            'usersCount' => 3
        ]));

        $this->assertInstanceOf(CreatePlatformResponse::class, $response);
        $this->assertEquals(0, sizeof($this->platformRepository->objects));
        $this->assertEquals(CheckPlatformSlugResponse::PLATFORM_SLUG_INVALID, $response->errorCode);
        $this->assertFalse($response->success);
    }
}