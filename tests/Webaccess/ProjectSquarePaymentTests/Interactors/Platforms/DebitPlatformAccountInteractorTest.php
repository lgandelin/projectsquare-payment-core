<?php

use Webaccess\ProjectSquarePayment\Interactors\Platforms\DebitPlatformAccountInteractor;
use Webaccess\ProjectSquarePayment\Requests\Platforms\DebitPlatformAccountRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\DebitPlatformAccountResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class DebitPlatformAccountBalanceInteractorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DebitPlatformAccountInteractor($this->platformRepository, $this->getLoggerMock());
    }

    public function testDebitPlatformAccount()
    {
        $platform = $this->createSamplePlatform();
        $response = $this->interactor->execute(new DebitPlatformAccountRequest([
            'platformID' => $platform->getID()
        ]));

        $this->assertInstanceOf(DebitPlatformAccountResponse::class, $response);
        $this->assertTrue($response->success);

        $platform = $this->platformRepository->getByID($platform->getID());
        $this->assertAmountEquals(58.34, $platform->getAccountBalance());
    }

    public function testDebitPlatformAccountWithNonExistingPlatform()
    {
        $response = $this->interactor->execute(new DebitPlatformAccountRequest([
            'amount' => 50.55
        ]));

        $this->assertInstanceOf(DebitPlatformAccountResponse::class, $response);
        $this->assertEquals(DebitPlatformAccountResponse::PLATFORM_NOT_FOUND_ERROR_CODE, $response->errorCode);
        $this->assertFalse($response->success);
    }
}