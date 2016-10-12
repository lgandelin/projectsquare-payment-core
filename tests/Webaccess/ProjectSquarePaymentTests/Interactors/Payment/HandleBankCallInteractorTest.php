<?php

use Webaccess\ProjectSquarePayment\Interactors\Payment\HandleBankCallInteractor;
use Webaccess\ProjectSquarePayment\Requests\Payment\HandleBankCallRequest;
use Webaccess\ProjectSquarePayment\Responses\Payment\HandleBankCallResponse;
use Webaccess\ProjectSquarePayment\Services\BankService;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class HandleBankCallInteractorTest extends ProjectsquareTestCase
{
    public function testHandleBankCallWithNonExistingTransactionIdentifier()
    {
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('checkSignature')->never()
            ->mock();

        $interactor = new HandleBankCallInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);

        $response = $interactor->execute(new HandleBankCallRequest([
            'transactionIdentifier' => '123',
            'amount' => 50,
        ]));

        $this->assertInstanceOf(HandleBankCallResponse::class, $response);
        $this->assertEquals(HandleBankCallResponse::TRANSACTION_NOT_FOUND_ERROR_CODE, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testHandleBankCallWithInvalidAmount()
    {
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('checkSignature')->never()
            ->mock();

        $interactor = new HandleBankCallInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);

        $this->createSampleTransaction('a5eb87x', 50.00);
        $response = $interactor->execute(new HandleBankCallRequest([
            'transactionIdentifier' => 'a5eb87x',
            'amount' => 50.56,
        ]));

        $this->assertInstanceOf(HandleBankCallResponse::class, $response);
        $this->assertEquals(HandleBankCallResponse::INVALID_AMOUNT_ERROR_CODE, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testHandleBankCallWithInvalidSignature()
    {
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('checkSignature')->once()->andReturn(false)
            ->mock();

        $interactor = new HandleBankCallInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);

        $this->createSampleTransaction('a5eb87x', 50.00);
        $response = $interactor->execute(new HandleBankCallRequest([
            'transactionIdentifier' => 'a5eb87x',
            'amount' => 50.00,
        ]));

        $this->assertInstanceOf(HandleBankCallResponse::class, $response);
        $this->assertEquals(HandleBankCallResponse::SIGNATURE_CHECK_FAILED_ERROR_CODE, $response->errorCode);
        $this->assertFalse($response->success);
    }
}