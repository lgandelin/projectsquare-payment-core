<?php

use Webaccess\ProjectSquarePayment\Interactors\Platforms\GetPlatformUsageAmountInteractor;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class GetPlatformUsageAmountInteractorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetPlatformUsageAmountInteractor($this->platformRepository, $this->getLoggerMock());
    }

    public function testGetMonthlyCost()
    {
        $platform = $this->createSamplePlatform();

        $this->assertAmountEquals(49.96, $this->interactor->getMonthlyCost($platform->getID()));
    }

    public function testGetDailyCost()
    {
        $platform = $this->createSamplePlatform();

        $this->assertAmountEquals(1.66, $this->interactor->getDailyCost($platform->getID()));
    }
}