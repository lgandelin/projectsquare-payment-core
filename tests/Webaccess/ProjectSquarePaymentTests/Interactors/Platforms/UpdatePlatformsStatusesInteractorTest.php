<?php

use Webaccess\ProjectSquarePayment\Entities\PlaStform;
use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Interactors\Platforms\UpdatePlatformsStatusesInteractor;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class UpdatePlatformsStatusesInteractorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdatePlatformsStatusesInteractor($this->platformRepository, $this->getLoggerMock());
    }

    public function testUpdatePlatformStatusAfterTrialPeriod()
    {
        $platform = $this->createSamplePlatform();
        $platform->setCreationDate(new \DateTime("2016-09-01 11:00:00"));

        $this->interactor->execute();

        $platform = $this->platformRepository->getByID($platform->getID());

        $this->assertEquals(Platform::PLATFORM_STATUS_IN_USE, $platform->getStatus());
    }

    public function testUpdatePlatformStatusWhenBalanceIsNegative()
    {
        $platform = $this->createSamplePlatform();
        $platform->setStatus(Platform::PLATFORM_STATUS_IN_USE);
        $platform->setAccountBalance(-2.00);

        $this->interactor->execute();

        $platform = $this->platformRepository->getByID($platform->getID());

        $this->assertEquals(Platform::PLATFORM_STATUS_DISABLED, $platform->getStatus());
    }
}