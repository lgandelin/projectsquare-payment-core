<?php

namespace Webaccess\ProjectSquarePaymentTests;

use Mockery;
use Webaccess\ProjectSquarePayment\Context;
use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Interactors\Platforms\CreatePlatformInteractor;
use Webaccess\ProjectSquarePayment\Repositories\InMemory\InMemoryAdministratorRepository;
use Webaccess\ProjectSquarePayment\Repositories\InMemory\InMemoryPlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\CreatePlatformRequest;

class ProjectsquareTestCase extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->platformRepository = new InMemoryPlatformRepository();
        $this->administratorRepository = new InMemoryAdministratorRepository();

        Context::setMonth(9);
        Context::setYear(2016);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function assertAmountEquals($expected, $actual)
    {
        $this->assertEquals($expected, $actual, '', 0.01);
    }

    protected function createSamplePlatform($name = 'Webaccess', $slug = 'webaccess')
    {
        $response = (new CreatePlatformInteractor($this->platformRepository))->execute(new CreatePlatformRequest([
            'name' => $name,
            'slug' => $slug,
            'usersCount' => 3,
            'platformMonthlyCost' => 19.99,
            'userMonthlyCost' => 9.99,
        ]));
        $platform = $this->platformRepository->getByID($response->platformID);
        $platform->setAccountBalance(60);
        $this->platformRepository->persist($platform);

        return $platform;
    }
}