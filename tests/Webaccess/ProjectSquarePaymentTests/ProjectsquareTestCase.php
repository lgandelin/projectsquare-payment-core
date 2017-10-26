<?php

namespace Webaccess\ProjectSquarePaymentTests;

use Mockery;
use Webaccess\ProjectSquarePayment\Context;
use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Entities\Administrator;
use Webaccess\ProjectSquarePayment\Entities\Node;
use Webaccess\ProjectSquarePayment\Entities\Transaction;
use Webaccess\ProjectSquarePayment\Interactors\Platforms\CreatePlatformInteractor;
use Webaccess\ProjectSquarePayment\Requests\Platforms\CreatePlatformRequest;
use Webaccess\ProjectSquarePaymentTests\Repositories\InMemory\InMemoryAdministratorRepository;
use Webaccess\ProjectSquarePaymentTests\Repositories\InMemory\InMemoryNodeRepository;
use Webaccess\ProjectSquarePaymentTests\Repositories\InMemory\InMemoryPlatformRepository;

class ProjectsquareTestCase extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->platformRepository = new InMemoryPlatformRepository();
        $this->administratorRepository = new InMemoryAdministratorRepository();
        $this->nodeRepository = new InMemoryNodeRepository();

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

    protected function createSampleAdministrator($platformID)
    {
        $administrator = new Administrator();
        $administrator->setLastName('Doe');
        $administrator->setFirstName('John');
        $administrator->setEmail('john.doe@gmail.com');
        $administrator->setBillingAddress('Billing address');
        $administrator->setCity('City');
        $administrator->setZipcode('Zipcode');
        $administrator->setPlatformID($platformID);
        $this->administratorRepository->persist($administrator);

        return $administrator;
    }

    protected function createSamplePlatform($name = 'Webaccess', $slug = 'webaccess')
    {
        $response = (new CreatePlatformInteractor($this->platformRepository, $this->getLoggerMock()))->execute(new CreatePlatformRequest([
            'name' => $name,
            'slug' => $slug,
            'usersCount' => 3,
            'platformMonthlyCost' => 19.99,
            'userMonthlyCost' => 9.99,
        ]));
        $platform = $this->platformRepository->getByID($response->platformID);
        $this->platformRepository->persist($platform);

        return $platform;
    }

    protected function createSampleNode($identifier = 'Node', $available = false)
    {
        $node = new Node();
        $node->setIdentifier($identifier);
        $node->setAvailable($available);
        $this->nodeRepository->persist($node);

        return $node;
    }

    protected function createSampleTransaction($identifier, $amount, $platformID = null)
    {
        $transaction = new Transaction();
        $transaction->setIdentifier($identifier);
        $transaction->setStatus(Transaction::TRANSACTION_STATUS_IN_PROGRESS);
        $transaction->setAmount($amount);
        $transaction->setPlatformID($platformID);

        return $transaction;
    }

    protected function getLoggerMock()
    {
        return Mockery::mock(Logger::class)
            ->shouldReceive('logRequest')
            ->shouldReceive('logResponse')
            ->mock();
    }
}