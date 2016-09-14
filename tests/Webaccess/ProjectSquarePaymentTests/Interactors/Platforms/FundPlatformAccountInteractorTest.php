<?php

use Webaccess\ProjectSquarePayment\Interactors\Platforms\FundPlatformAccountInteractor;
use Webaccess\ProjectSquarePayment\Requests\Platforms\FundPlatformAccountRequest;
use Webaccess\ProjectSquarePayment\Responses\Platforms\FundPlatformAccountResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class FundPlatformAccountInteractorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new FundPlatformAccountInteractor($this->platformRepository);
    }

    public function testFundPlatformAccount()
    {
        $platform = $this->createSamplePlatform();
        $response = $this->interactor->execute(new FundPlatformAccountRequest([
            'platformID' => $platform->getID(),
            'amount'=> 35.18
        ]));

        $this->assertInstanceOf(FundPlatformAccountResponse::class, $response);
        $this->assertTrue($response->success);

        $platform = $this->platformRepository->getByID($platform->getID());
        $this->assertAmountEquals(95.18, $platform->getAccountBalance());
    }

    public function testFundPlatformAccountWithNonExistingPlatform()
    {
        $response = $this->interactor->execute(new FundPlatformAccountRequest([
            'platformID' => 1,
            'amount' => 50.55
        ]));

        $this->assertInstanceOf(FundPlatformAccountResponse::class, $response);
        $this->assertEquals(FundPlatformAccountResponse::PLATFORM_NOT_FOUND_ERROR_CODE, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testFundPlatformAccountWithNegativeAmount()
    {
        $platform = $this->createSamplePlatform();
        $response = $this->interactor->execute(new FundPlatformAccountRequest([
            'platformID' => $platform->getID(),
            'amount'=> -25.18
        ]));

        $this->assertInstanceOf(FundPlatformAccountResponse::class, $response);
        $this->assertAmountEquals(60, $platform->getAccountBalance());
        $this->assertEquals(FundPlatformAccountResponse::INVALID_AMOUNT_ERROR_CODE, $response->errorCode);
        $this->assertFalse($response->success);
    }
}