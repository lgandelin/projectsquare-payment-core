<?php

use Webaccess\ProjectSquarePayment\Interactors\Infrastructure\CreateInfrastructureInteractor;
use Webaccess\ProjectSquarePayment\Requests\Infrastructure\CreateInfrastructureRequest;
use Webaccess\ProjectSquarePayment\Responses\Infrastructure\CreateInfrastructureResponse;
use Webaccess\ProjectSquarePayment\Services\RemoteInfrastructureGenerator;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class CreateInfrastructureInteractorTest extends ProjectsquareTestCase
{
    public function testCreateNewInfrastructure()
    {
        $remoteInfrastructureGeneratorMock = Mockery::mock(RemoteInfrastructureGenerator::class)
            ->shouldReceive('launchEnvCreation')->once()->with(Mockery::type('string'), 'webaccess', 'lgandelin@web-access.fr', 3)
            ->shouldReceive('launchAppCreation')->never()
            ->mock();

        $interactor = new CreateInfrastructureInteractor($this->nodeRepository, $remoteInfrastructureGeneratorMock);

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

        $remoteInfrastructureGeneratorMock = Mockery::mock(RemoteInfrastructureGenerator::class)
            ->shouldReceive('launchEnvCreation')->never()
            ->shouldReceive('launchAppCreation')->once()->with('availableNode', 'webaccess', 'lgandelin@web-access.fr', 3)
            ->mock();

        $interactor = new CreateInfrastructureInteractor($this->nodeRepository, $remoteInfrastructureGeneratorMock);

        $response = $interactor->execute(new CreateInfrastructureRequest([
            'slug' => 'webaccess',
            'administratorEmail' => 'lgandelin@web-access.fr',
            'usersLimit' => 3,
        ]));

        $this->assertInstanceOf(CreateInfrastructureResponse::class, $response);
        $this->assertEquals(2, sizeof($this->nodeRepository->objects));
        $this->assertTrue($response->success);
    }
}