<?php

use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Interactors\GetPlatformUsageAmountInteractor;
use Webaccess\ProjectSquarePayment\Repositories\InMemory\InMemoryPlatformRepository;

class GetPlatformUsageAmountInteractorTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->platformRepository = new InMemoryPlatformRepository();
        $this->interactor = new GetPlatformUsageAmountInteractor($this->platformRepository);
    }

    public function testGetMonthlyCost()
    {
        $platform = $this->createSamplePlatform();

        $this->assertEquals(49.96, $this->interactor->getMonthlyCost($platform->getID()), '', 0.01);
    }

    public function testGetDailyCost()
    {
        $platform = $this->createSamplePlatform();

        $this->assertEquals(1.66, $this->interactor->getDailyCost($platform->getID()), '', 0.01);
    }

    private function createSamplePlatform()
    {
        $platform = new Platform();
        $platform->setFixedMonthlyCost(19.99);
        $platform->setUserMonthlyCost(9.99);
        $platform->setUsersCount(3);
        $this->platformRepository->persist($platform);

        return $platform;
    }
}