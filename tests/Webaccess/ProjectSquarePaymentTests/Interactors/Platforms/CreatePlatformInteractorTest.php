<?php

use Webaccess\ProjectSquarePayment\Interactors\Platforms\CreatePlatformInteractor;
use Webaccess\ProjectSquarePayment\Requests\Platforms\CreatePlatformRequest;
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
    }
}