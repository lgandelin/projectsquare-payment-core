<?php

use Webaccess\ProjectSquarePayment\Contracts\RemoteInfrastructureService;
use Webaccess\ProjectSquarePayment\Interactors\Infrastructure\CreateInfrastructureInteractor;
use Webaccess\ProjectSquarePayment\Requests\Infrastructure\CreateInfrastructureRequest;
use Webaccess\ProjectSquarePayment\Responses\Infrastructure\CreateInfrastructureResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class CreateInfrastructureInteractorTest extends ProjectsquareTestCase
{
    public function testCreateNewInfrastructure()
    {
        $remoteInfrastructureGeneratorMock = Mockery::mock(RemoteInfrastructureService::class)
            ->shouldReceive('launchEnvCreation')->once()->with(Mockery::type('string'), 'webaccess', 'lgandelin@web-access.fr', 3)
            ->shouldReceive('launchNodeCreation')->once()->with(Mockery::type('string'))
            ->shouldReceive('launchAppCreation')->never()
            ->mock();

        $interactor = new CreateInfrastructureInteractor($this->nodeRepository, $this->platformRepository, $remoteInfrastructureGeneratorMock, $this->getLoggerMock());

        $response = $interactor->execute(new CreateInfrastructureRequest([
            'slug' => 'webaccess',
            'administratorEmail' => 'lgandelin@web-access.fr',
            'usersLimit' => 3,
        ]));

        $this->assertInstanceOf(CreateInfrastructureResponse::class, $response);
        $this->assertEquals(2, sizeof($this->nodeRepository->objects));
        $this->assertTrue($response->success);
    }

    public function testCreateNewInfrastructureWithAvailableNode()
    {
        $this->createSampleNode('availableNode', true);

        $remoteInfrastructureGeneratorMock = Mockery::mock(RemoteInfrastructureService::class)
            ->shouldReceive('launchEnvCreation')->never()
            ->shouldReceive('launchNodeCreation')->once()->with(Mockery::type('string'))
            ->shouldReceive('launchAppCreation')->once()->with('availableNode', 'webaccess', 'lgandelin@web-access.fr', 3)
            ->mock();

        $interactor = new CreateInfrastructureInteractor($this->nodeRepository, $this->platformRepository, $remoteInfrastructureGeneratorMock, $this->getLoggerMock());

        $response = $interactor->execute(new CreateInfrastructureRequest([
            'slug' => 'webaccess',
            'administratorEmail' => 'lgandelin@web-access.fr',
            'usersLimit' => 3,
        ]));

        $this->assertInstanceOf(CreateInfrastructureResponse::class, $response);
        $this->assertEquals(2, sizeof($this->nodeRepository->objects));
        $this->assertEquals(false, $this->nodeRepository->objects[1]->isAvailable());
        $this->assertTrue($response->success);
    }
}