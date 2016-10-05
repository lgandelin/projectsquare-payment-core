<?php

use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Interactors\Platforms\UpdatePlatformUsersCountInteractor;
use Webaccess\ProjectSquarePayment\Repositories\RemotePlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\UpdatePlatformUsersCountRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\UpdatePlatformUsersCountResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class UpdatePlatformUsersCountInteractorTest extends ProjectsquareTestCase
{
    private $interactor;

    public function testUpdatePlatformUsersCount()
    {
        $platform = $this->createSamplePlatform();

        $projectsquareAPIMock = Mockery::mock(RemotePlatformRepository::class)
            ->shouldReceive('getUsersLimit')->once()->andReturn(3)
            ->shouldReceive('updateUsersLimit')->once()->with(Mockery::type(Platform::class), 4)
            ->mock();

        $this->interactor = new UpdatePlatformUsersCountInteractor($this->platformRepository, $projectsquareAPIMock);

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
        $projectsquareAPIMock = Mockery::mock(RemotePlatformRepository::class)
            ->shouldReceive('getUsersLimit')->never()
            ->shouldReceive('updateUsersLimit')->never()
            ->mock();

        $this->interactor = new UpdatePlatformUsersCountInteractor($this->platformRepository, $projectsquareAPIMock);

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

        $projectsquareAPIMock = Mockery::mock(RemotePlatformRepository::class)
            ->shouldReceive('getUsersLimit')->never()
            ->shouldReceive('updateUsersLimit')->never()
            ->mock();

        $this->interactor = new UpdatePlatformUsersCountInteractor($this->platformRepository, $projectsquareAPIMock);

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

        $projectsquareAPIMock = Mockery::mock(RemotePlatformRepository::class)
            ->shouldReceive('getUsersLimit')->once()->andReturn(3)
            ->shouldReceive('updateUsersLimit')->never()
            ->mock();

        $this->interactor = new UpdatePlatformUsersCountInteractor($this->platformRepository, $projectsquareAPIMock);

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
        $platform = $this->createSamplePlatform();

        $projectsquareAPIMock = Mockery::mock(RemotePlatformRepository::class)
            ->shouldReceive('getUsersLimit')->once()->andReturn(null)
            ->shouldReceive('updateUsersLimit')->never()
            ->mock();

        $this->interactor = new UpdatePlatformUsersCountInteractor($this->platformRepository, $projectsquareAPIMock);

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