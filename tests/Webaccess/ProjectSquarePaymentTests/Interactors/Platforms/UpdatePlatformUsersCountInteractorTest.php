<?php

use Webaccess\ProjectSquarePayment\Interactors\Platforms\UpdatePlatformUsersCountInteractor;
use Webaccess\ProjectSquarePayment\Requests\Platforms\UpdatePlatformUsersCountRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\UpdatePlatformUsersCountResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class UpdatePlatformUsersCountInteractorTest extends ProjectsquareTestCase
{
    public function testUpdatePlatformUsersCount()
    {
        $platform = $this->createSamplePlatform();

        $interactor = new UpdatePlatformUsersCountInteractor($this->platformRepository, $this->getLoggerMock());

        $response = $interactor->execute(new UpdatePlatformUsersCountRequest([
            'platformID' => $platform->getId(),
            'usersCount' => 4,
        ]));

        $this->assertInstanceOf(UpdatePlatformUsersCountResponse::class, $response);
        $this->assertTrue($response->success);
        $platform = $this->platformRepository->getByID($platform->getId());
        $this->assertEquals(4, $platform->getUsersCount());
    }

    public function testUpdatePlatformUsersCountWithoutPlatform()
    {
        $interactor = new UpdatePlatformUsersCountInteractor($this->platformRepository, $this->getLoggerMock());

        $response = $interactor->execute(new UpdatePlatformUsersCountRequest([
            'usersCount' => 2,
        ]));

        $this->assertInstanceOf(UpdatePlatformUsersCountResponse::class, $response);
        $this->assertEquals(UpdatePlatformUsersCountResponse::PLATFORM_NOT_FOUND_ERROR_CODE, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testUpdatePlatformUsersCountWithInvalidCount()
    {
        $platform = $this->createSamplePlatform();

        $interactor = new UpdatePlatformUsersCountInteractor($this->platformRepository, $this->getLoggerMock());

        $response = $interactor->execute(new UpdatePlatformUsersCountRequest([
            'platformID' => $platform->getId(),
            'usersCount' => -5,
        ]));

        $this->assertInstanceOf(UpdatePlatformUsersCountResponse::class, $response);
        $this->assertFalse($response->success);
        $this->assertEquals(UpdatePlatformUsersCountResponse::INVALID_USERS_COUNT, $response->errorCode);
        $platform = $this->platformRepository->getByID($platform->getId());
        $this->assertEquals(3, $platform->getUsersCount());
    }
}