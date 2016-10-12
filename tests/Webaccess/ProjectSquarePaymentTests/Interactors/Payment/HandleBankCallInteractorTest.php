<?php

use Webaccess\ProjectSquarePayment\Entities\Transaction;
use Webaccess\ProjectSquarePayment\Interactors\Payment\HandleBankCallInteractor;
use Webaccess\ProjectSquarePayment\Requests\Payment\HandleBankCallRequest;
use Webaccess\ProjectSquarePayment\Responses\Payment\HandleBankCallResponse;
use Webaccess\ProjectSquarePayment\Services\BankService;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class HandleBankCallInteractorTest extends ProjectsquareTestCase
{
    public function testHandleBankCall()
    {
        $transactionIdentifier = 'a5eb87x';
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('checkSignature')->once()->andReturn(true)
            ->mock();

        $interactor = new HandleBankCallInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);

        $platform = $this->createSamplePlatform();
        $this->createSampleTransaction($transactionIdentifier, 50.00, $platform->getID());

        $response = $interactor->execute(new HandleBankCallRequest([
            'transactionIdentifier' => $transactionIdentifier,
            'amount' => 50.00,
            'parameters' => [
                'paymentMeanType' => 'CB',
                'paymentMeanBrand' => 'Mastercard',
                'responseCode' => '00',
            ]
        ]));

        $platform = $this->platformRepository->getByID($platform->getID());

        $this->assertInstanceOf(HandleBankCallResponse::class, $response);
        $this->assertTrue($response->success);
        $this->assertAmountEquals(110.00, $platform->getAccountBalance());

        $transaction = $this->transactionRepository->getByIdentifier($transactionIdentifier);
        $this->assertEquals(Transaction::TRANSACTION_STATUS_VALIDATED, $transaction->getStatus());
        $this->assertEquals('CB - Mastercard', $transaction->getPaymentMean());
        $this->assertEquals('00', $transaction->getResponseCode());
    }

    public function testHandleBankTwoCallsForSameTransaction()
    {
        $transactionIdentifier = 'a5eb87x';
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('checkSignature')->times(2)->andReturn(true)
            ->mock();

        $interactor = new HandleBankCallInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);

        $platform = $this->createSamplePlatform();
        $this->createSampleTransaction($transactionIdentifier, 50.00, $platform->getID());

        $interactor->execute(new HandleBankCallRequest([
            'transactionIdentifier' => $transactionIdentifier,
            'amount' => 50.00,
            'parameters' => [
                'responseCode' => '00',
            ]
        ]));

        $response = $interactor->execute(new HandleBankCallRequest([
            'transactionIdentifier' => $transactionIdentifier,
            'amount' => 50.00,
            'parameters' => [
                'responseCode' => '00',
            ]
        ]));

        $platform = $this->platformRepository->getByID($platform->getID());

        $this->assertInstanceOf(HandleBankCallResponse::class, $response);
        $this->assertTrue($response->success);
        $this->assertAmountEquals(110.00, $platform->getAccountBalance());

        $transaction = $this->transactionRepository->getByIdentifier($transactionIdentifier);
        $this->assertEquals(Transaction::TRANSACTION_STATUS_VALIDATED, $transaction->getStatus());
    }

    public function testHandleBankCallWithInvalidResponseCode()
    {
        $transactionIdentifier = 'a5eb87x';
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('checkSignature')->once()->andReturn(true)
            ->mock();

        $interactor = new HandleBankCallInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);

        $platform = $this->createSamplePlatform();
        $this->createSampleTransaction($transactionIdentifier, 50.00, $platform->getID());

        $response = $interactor->execute(new HandleBankCallRequest([
            'transactionIdentifier' => $transactionIdentifier,
            'amount' => 50.00,
            'parameters' => [
                'paymentMeanType' => 'CB',
                'paymentMeanBrand' => 'Mastercard',
                'responseCode' => '05',
            ]
        ]));

        $platform = $this->platformRepository->getByID($platform->getID());

        $this->assertInstanceOf(HandleBankCallResponse::class, $response);
        $this->assertFalse($response->success);
        $this->assertAmountEquals(60.00, $platform->getAccountBalance());

        $transaction = $this->transactionRepository->getByIdentifier($transactionIdentifier);
        $this->assertEquals(Transaction::TRANSACTION_STATUS_ERROR, $transaction->getStatus());
        $this->assertEquals('CB - Mastercard', $transaction->getPaymentMean());
        $this->assertEquals('05', $transaction->getResponseCode());
    }

    public function testHandleBankCallWithNonExistingTransactionIdentifier()
    {
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('checkSignature')->never()
            ->mock();

        $interactor = new HandleBankCallInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);

        $response = $interactor->execute(new HandleBankCallRequest([
            'transactionIdentifier' => '123',
            'amount' => 50.00,
            'parameters' => [
                'responseCode' => '00',
            ]
        ]));

        $this->assertInstanceOf(HandleBankCallResponse::class, $response);
        $this->assertEquals(HandleBankCallResponse::TRANSACTION_NOT_FOUND_ERROR_CODE, $response->errorCode);
        $this->assertFalse($response->success);
    }

    public function testHandleBankCallWithInvalidAmount()
    {
        $transactionIdentifier = 'a5eb87x';
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('checkSignature')->never()
            ->mock();

        $interactor = new HandleBankCallInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);

        $this->createSampleTransaction($transactionIdentifier, 50.00);
        $response = $interactor->execute(new HandleBankCallRequest([
            'transactionIdentifier' => $transactionIdentifier,
            'amount' => 50.56,
            'parameters' => [
                'responseCode' => '00',
            ]
        ]));

        $this->assertInstanceOf(HandleBankCallResponse::class, $response);
        $this->assertEquals(HandleBankCallResponse::INVALID_AMOUNT_ERROR_CODE, $response->errorCode);
        $this->assertFalse($response->success);

        $transaction = $this->transactionRepository->getByIdentifier($transactionIdentifier);
        $this->assertEquals(Transaction::TRANSACTION_STATUS_ERROR, $transaction->getStatus());
    }

    public function testHandleBankCallWithInvalidSignature()
    {
        $transactionIdentifier = 'a5eb87x';
        $bankServiceMock = Mockery::mock(BankService::class)
            ->shouldReceive('checkSignature')->once()->andReturn(false)
            ->mock();

        $interactor = new HandleBankCallInteractor($this->platformRepository, $this->transactionRepository, $bankServiceMock);

        $this->createSampleTransaction($transactionIdentifier, 50.00);
        $response = $interactor->execute(new HandleBankCallRequest([
            'transactionIdentifier' => $transactionIdentifier,
            'amount' => 50.00,
            'parameters' => [
                'responseCode' => '00',
            ]
        ]));

        $this->assertInstanceOf(HandleBankCallResponse::class, $response);
        $this->assertEquals(HandleBankCallResponse::SIGNATURE_CHECK_FAILED_ERROR_CODE, $response->errorCode);
        $this->assertFalse($response->success);

        $transaction = $this->transactionRepository->getByIdentifier($transactionIdentifier);
        $this->assertEquals(Transaction::TRANSACTION_STATUS_ERROR, $transaction->getStatus());
    }
}