<?php

use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Interactors\Platforms\DecreasePlatformAccountBalanceInteractor;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class DecreasePlatformAccountBalanceInteractorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DecreasePlatformAccountBalanceInteractor($this->platformRepository);
    }

    public function testDecreasePlatformAccountBalanceDaily()
    {
        $platform = $this->createSamplePlatform();
        $this->interactor->decreaseDailyCost($platform->getID());
        $this->platformRepository->getByID($platform->getID());

        $this->assertAmountEquals(58.34, $platform->getAccountBalance());
    }

    public function testDecreasePlatformAccountBalanceMonthly()
    {
        $platform = $this->createSamplePlatform();
        $this->interactor->decreaseMonthlyCost($platform->getID());
        $this->platformRepository->getByID($platform->getID());

        $this->assertAmountEquals(10.04, $platform->getAccountBalance());
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