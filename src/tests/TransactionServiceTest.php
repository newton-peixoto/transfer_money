<?php

use App\Jobs\SendTransactionNotificationJob;
use App\Models\User;
use App\Models\Wallet;
use App\Services\AuthorizationService;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Queue;


class TransactionServiceTest extends TestCase
{

    public function setUp()  : void {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    public function testMustNotTransferWhenServiceIsNotAvailable()
    {
        $this->expectExceptionMessage('Service is not available. Try again later.');
        $mock = Mockery::mock(AuthorizationService::class);
        $mock->shouldReceive('isServiceAvailable')->andReturn(FALSE);
        $transactionService = new TransactionService($mock);
        $transactionService->transfer(['payee' => 1, 'payer' => 2, 'amount'=> 100]);    
    }

    public function testMustNotTransferWhenUserDoesNotHaveEnoughMoney()
    {
        $this->expectExceptionMessage('The requested amount is not available');
        // prepara o mock do serviço e cria dois usuários comuns
        $payer =User::factory(1)->has(Wallet::factory())->create([
            'identifier' => '26791226072',
            'user_type'  => 'customer',
        ])->first();
        $payee =User::factory(1)->has(Wallet::factory())->create([
            'identifier' => '72902300000',
            'user_type'  => 'customer'
        ])->first();
        $mock = Mockery::mock(AuthorizationService::class);
        $mock->shouldReceive('isServiceAvailable')->andReturn(True);
        $transactionService = new TransactionService($mock);
        $transactionService->transfer(['payer' => $payer->id, 'payee' => $payee->id, 'amount'=> 99999]);    
    }

    public function testTheMoneyMustBeTransferedWhenGivenDataIsValidAndServiceIsAvailable() {

        Queue::fake();

        $payer =User::factory(1)->has(Wallet::factory())->create([
            'identifier' => '26791226072',
            'user_type'  => 'customer',
        ])->first();
        $payee =User::factory(1)->has(Wallet::factory())->create([
            'identifier' => '72902300000',
            'user_type'  => 'customer'
        ])->first();

        $payerCurrentAmount = $payer->wallet->balance;
        $payeeCurrentAmount = $payee->wallet->balance;
        $transferedAmount   = 50;

        $mock = Mockery::mock(AuthorizationService::class);
        $mock->shouldReceive('isServiceAvailable')->andReturn(True);
        $transactionService = new TransactionService($mock);
        $transactionService->transfer(['payer' => $payer->id, 'payee' => $payee->id, 'amount'=> $transferedAmount]);

        Queue::assertPushed(SendTransactionNotificationJob::class, 1);
        
        $this->assertEquals( $payer->wallet()->first()->balance, $payerCurrentAmount - $transferedAmount);
        $this->assertEquals( $payee->wallet()->first()->balance, $payeeCurrentAmount + $transferedAmount);

    }
}
