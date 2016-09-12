<?php

use Webaccess\ProjectSquarePayment\Interactors\Platforms\CreatePlatformInteractor;
use Webaccess\ProjectSquarePayment\Requests\Platforms\CreatePlatformRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse;
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

        $this->assertInstanceOf(Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse::class, $response);
        $this->assertEquals(1, sizeof($this->platformRepository->objects));
        $this->assertEquals('Webaccess', $response->platform->getName());
        $this->assertEquals('webaccess', $response->platform->getSlug());
        $this->assertEquals(3, $response->platform->getUsersCount());
        $this->assertTrue($response->success);
    }

    public function testCreatePlatformWithoutName()
    {
        $response = $this->interactor->execute(new CreatePlatformRequest([
            'slug' => 'webaccess',
            'usersCount' => 3
        ]));

        $this->assertInstanceOf(Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse::class, $response);
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

        $this->assertInstanceOf(Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse::class, $response);
        $this->assertEquals(1, sizeof($this->platformRepository->objects));
        $this->assertEquals(CreatePlatformResponse::PLATFORM_SLUG_UNAVAILABLE, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testCreatePlatformWithoutUsers()
    {
        $response = $this->interactor->execute(new CreatePlatformRequest([
            'name' => 'Webaccess',
            'slug' => 'webaccess',
        ]));

        $this->assertInstanceOf(Webaccess\ProjectSquarePayment\Responses\Platforms\CreatePlatformResponse::class, $response);
        $this->assertEquals(0, sizeof($this->platformRepository->objects));
        $this->assertEquals(CreatePlatformResponse::PLATFORM_USERS_COUNT_REQUIRED, $response->errorCode);
        $this->assertFalse($response->success);
    }
}