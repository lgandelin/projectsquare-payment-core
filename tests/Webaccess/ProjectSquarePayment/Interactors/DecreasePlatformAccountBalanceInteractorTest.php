<?php

use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Interactors\DecreasePlatformAccountBalanceInteractor;
use Webaccess\ProjectSquarePayment\Repositories\InMemory\InMemoryPlatformRepository;

class DecreasePlatformAccountBalanceInteractorTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->platformRepository = new InMemoryPlatformRepository();
        $this->interactor = new DecreasePlatformAccountBalanceInteractor($this->platformRepository);
    }

    public function testDecreasePlatformAccountBalanceDaily()
    {
        $platform = $this->createSamplePlatform();
        $this->interactor->decreaseDailyCost($platform->getID());
        $this->platformRepository->getByID($platform->getID());

        $this->assertEquals(58.34, $platform->getAccountBalance(), '', 0.01);
    }

    public function testDecreasePlatformAccountBalanceMonthly()
    {
        $platform = $this->createSamplePlatform();
        $this->interactor->decreaseMonthlyCost($platform->getID());
        $this->platformRepository->getByID($platform->getID());

        $this->assertEquals(10.04, $platform->getAccountBalance(), '', 0.01);
    }

    private function createSamplePlatform()
    {
        $platform = new Platform();
        $platform->setFixedMonthlyCost(19.99);
        $platform->setUserMonthlyCost(9.99);
        $platform->setUsersCount(3);
        $platform->setAccountBalance(60);
        $this->platformRepository->persist($platform);

        return $platform;
    }
}