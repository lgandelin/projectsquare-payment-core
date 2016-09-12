<?php

namespace Webaccess\ProjectSquarePaymentTests;

use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Repositories\InMemory\InMemoryPlatformRepository;

class ProjectsquareTestCase extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->platformRepository = new InMemoryPlatformRepository();
    }

    public function assertAmountEquals($expected, $actual)
    {
        $this->assertEquals($expected, $actual, '', 0.01);
    }

    protected function createSamplePlatform()
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