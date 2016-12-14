<?php

use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Interactors\Platforms\DebitPlatformsAccountsInteractor;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class DebitPlatformsAccountsInteractorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DebitPlatformsAccountsInteractor($this->platformRepository, $this->getLoggerMock());
    }

    public function testDebitPlatforms()
    {
        $platform1 = $this->createSamplePlatform('platform1', 'platform1');
        $platform1->setUsersCount(5);
        $platform1->setAccountBalance(50);
        $platform1->setStatus(Platform::PLATFORM_STATUS_IN_USE);
        $this->platformRepository->persist($platform1);

        $platform2 = $this->createSamplePlatform('platform2', 'platform2');
        $platform2->setUsersCount(12);
        $platform2->setAccountBalance(187.21);
        $platform2->setStatus(Platform::PLATFORM_STATUS_IN_USE);
        $this->platformRepository->persist($platform2);

        $this->interactor->execute();

        $platform1 = $this->platformRepository->getByID($platform1->getId());
        $platform2 = $this->platformRepository->getByID($platform2->getId());

        $this->assertAmountEquals(47.67, $platform1->getAccountBalance());
        $this->assertAmountEquals(182.55, $platform2->getAccountBalance());
    }

    public function testDebitValidPlatformsOnly()
    {
        $platform1 = $this->createSamplePlatform('platform1', 'platform1');
        $platform1->setStatus(Platform::PLATFORM_STATUS_IN_USE);
        $platform1->setUsersCount(5);
        $platform1->setAccountBalance(50);

        $platform2 = $this->createSamplePlatform('platform2', 'platform2');
        $platform2->setStatus(Platform::PLATFORM_STATUS_IN_USE);
        $platform2->setUsersCount(12);
        $platform2->setAccountBalance(187.21);

        $platform3 = $this->createSamplePlatform('platform3', 'platform3');
        $this->platformRepository->persist($platform3);

        $platform4 = $this->createSamplePlatform('platform4', 'platform4');
        $platform4->setStatus(Platform::PLATFORM_STATUS_DISABLED);
        $this->platformRepository->persist($platform4);

        $this->interactor->execute();

        $platform1 = $this->platformRepository->getByID($platform1->getId());
        $platform2 = $this->platformRepository->getByID($platform2->getId());
        $platform3 = $this->platformRepository->getByID($platform3->getId());
        $platform4 = $this->platformRepository->getByID($platform4->getId());

        $this->assertAmountEquals(47.67, $platform1->getAccountBalance());
        $this->assertAmountEquals(182.55, $platform2->getAccountBalance());
        $this->assertAmountEquals(60.00, $platform3->getAccountBalance());
        $this->assertAmountEquals(60.00, $platform4->getAccountBalance());
    }
}