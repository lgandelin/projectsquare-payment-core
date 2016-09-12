<?php

namespace Webaccess\ProjectSquarePaymentTests;

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
}