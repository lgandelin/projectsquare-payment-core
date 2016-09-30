<?php

use Webaccess\ProjectSquarePayment\Interactors\Platforms\DebitPlatformsAccountsInteractor;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class DebitPlatformsAccountsInteractorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DebitPlatformsAccountsInteractor($this->platformRepository);
    }

    public function testDebitPlatforms()
    {
        $platform1 = $this->createSamplePlatform();
        $platform1->setUsersCount(5);
        $platform1->setAccountBalance(50);

        $platform2 = $this->createSamplePlatform();
        $platform2->setUsersCount(12);
        $platform2->setAccountBalance(187.21);

        $this->interactor->execute();

        $platform1 = $this->platformRepository->getByID($platform1->getId());
        $platform2 = $this->platformRepository->getByID($platform2->getId());

        $this->assertAmountEquals(47.67, $platform1->getAccountBalance());
        $this->assertAmountEquals(182.55, $platform2->getAccountBalance());
    }
}