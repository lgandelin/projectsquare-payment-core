<?php

use Webaccess\ProjectSquarePayment\Interactors\Platforms\UpdatePlatformUsersCountInteractor;
use Webaccess\ProjectSquarePayment\Requests\Platforms\UpdatePlatformUsersCountRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\UpdatePlatformUsersCountResponse;
use Webaccess\ProjectSquarePayment\Services\ProjectsquareAPIMock;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class UpdatePlatformUsersCountInteractorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdatePlatformUsersCountInteractor($this->platformRepository, new ProjectsquareAPIMock(3));
    }

    public function testUpdatePlatformUsersCount()
    {
        $platform = $this->createSamplePlatform();
        $response = $this->interactor->execute(new UpdatePlatformUsersCountRequest([
            'platformID' => $platform->getId(),
            'usersCount' => 4,
        ]));

        $this->assertInstanceOf(UpdatePlatformUsersCountResponse::class, $response);
        $this->assertTrue($response->success);
        $platform = $this->platformRepository->getByID($platform->getID());
        $this->assertEquals(4, $platform->getUsersCount());
    }

    public function testUpdatePlatformUsersCountWithoutPlatform()
    {
        $response = $this->interactor->execute(new UpdatePlatformUsersCountRequest([
            'usersCount' => 2,
        ]));

        $this->assertInstanceOf(UpdatePlatformUsersCountResponse::class, $response);
        $this->assertEquals(UpdatePlatformUsersCountResponse::PLATFORM_NOT_FOUND_ERROR_CODE, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testUpdatePlatformUsersCountWithInvalidCount()
    {
        $platform = $this->createSamplePlatform();
        $response = $this->interactor->execute(new UpdatePlatformUsersCountRequest([
            'platformID' => $platform->getID(),
            'usersCount' => -5,
        ]));

        $this->assertInstanceOf(UpdatePlatformUsersCountResponse::class, $response);
        $this->assertFalse($response->success);
        $this->assertEquals(UpdatePlatformUsersCountResponse::INVALID_USERS_COUNT, $response->errorCode);
        $platform = $this->platformRepository->getByID($platform->getID());
        $this->assertEquals(3, $platform->getUsersCount());
    }

    public function testUpdatePlatformUsersCountWithTooManyActualUsers()
    {
        $platform = $this->createSamplePlatform();
        $response = $this->interactor->execute(new UpdatePlatformUsersCountRequest([
            'platformID' => $platform->getID(),
            'usersCount' => 2,
        ]));

        $this->assertInstanceOf(UpdatePlatformUsersCountResponse::class, $response);
        $this->assertFalse($response->success);
        $this->assertEquals(UpdatePlatformUsersCountResponse::ACTUAL_USERS_COUNT_TOO_BIG_ERROR, $response->errorCode);
        $platform = $this->platformRepository->getByID($platform->getID());
        $this->assertEquals(3, $platform->getUsersCount());
    }

    public function testUpdatePlatformUsersCountWithNullActualCount()
    {
        $this->interactor = new UpdatePlatformUsersCountInteractor($this->platformRepository, new ProjectsquareAPIMock(null));
        $platform = $this->createSamplePlatform();
        $response = $this->interactor->execute(new UpdatePlatformUsersCountRequest([
            'platformID' => $platform->getID(),
            'usersCount' => 2,
        ]));

        $this->assertInstanceOf(UpdatePlatformUsersCountResponse::class, $response);
        $this->assertFalse($response->success);
        $this->assertEquals(UpdatePlatformUsersCountResponse::INVALID_ACTUAL_USERS_COUNT, $response->errorCode);
        $platform = $this->platformRepository->getByID($platform->getID());
        $this->assertEquals(3, $platform->getUsersCount());
    }
}