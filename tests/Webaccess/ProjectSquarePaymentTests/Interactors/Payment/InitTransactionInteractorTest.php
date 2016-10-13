<?php

use Webaccess\ProjectSquarePayment\Contracts\BankService;
use Webaccess\ProjectSquarePayment\Entities\Transaction;
use Webaccess\ProjectSquarePayment\Interactors\Payment\InitTransactionInteractor;
use Webaccess\ProjectSquarePayment\Requests\Payment\InitTransactionRequest;
use Webaccess\ProjectSquarePayment\Responses\Payment\InitTransactionResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class InitTransactionInteractorTest extends ProjectsquareTestCase
{
    public function testInitTransaction()
    {
        $platform = $this->createSamplePlatform();
        $amount = 56.00;
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('generateFormFields')->once()->with(Mockery::type('string'), $amount)->andReturn(['data', 'seal'])
            ->mock();
        $interactor = new InitTransactionInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);
        $response = $interactor->execute(new InitTransactionRequest([
            'platformID' => $platform->getID(),
            'amount' => $amount,
        ]));

        $this->assertInstanceOf(InitTransactionResponse::class, $response);
        $this->assertTrue($response->success);

        $transaction = $this->transactionRepository->getByIdentifier($response->transactionIdentifier);
        $this->assertNotEmpty($transaction->getIdentifier());
        $this->assertEquals($platform->getID(), $transaction->getPlatformID());
        $this->assertEquals(56.00, $transaction->getAmount());
        $this->assertEquals(Transaction::TRANSACTION_STATUS_IN_PROGRESS, $transaction->getStatus());

        $this->assertEquals('data', $response->data);
        $this->assertEquals('seal', $response->seal);
    }

    public function testInitTransactionWithInvalidPlatform()
    {
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('generateFormFields')->never()
            ->mock();
        $interactor = new InitTransactionInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);
        $response = $interactor->execute(new InitTransactionRequest([
            'platformID' => 2,
            'amount' => 56.00,
        ]));

        $this->assertInstanceOf(InitTransactionResponse::class, $response);
        $this->assertFalse($response->success);
        $this->assertEquals(InitTransactionResponse::PLATFORM_NOT_FOUND_ERROR, $response->errorCode);
        $this->assertEquals(0, count($this->transactionRepository->objects));
    }

    public function testInitTransactionWithInvalidAmount()
    {
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('generateFormFields')->never()
            ->mock();
        $platform = $this->createSamplePlatform();
        $interactor = new InitTransactionInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);
        $response = $interactor->execute(new InitTransactionRequest([
            'platformID' => $platform->getID(),
            'amount' => -200.00,
        ]));

        $this->assertInstanceOf(InitTransactionResponse::class, $response);
        $this->assertFalse($response->success);
        $this->assertEquals(InitTransactionResponse::INVALID_AMOUNT_ERROR, $response->errorCode);
        $this->assertEquals(0, count($this->transactionRepository->objects));
    }
}