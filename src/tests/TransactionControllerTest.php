<?php

use App\Models\User;
use App\Models\Wallet;
use App\Services\AuthorizationService;
use App\Jobs\SendTransactionNotificationJob;

class TransactionControllerTest extends TestCase
{

    public function setUp()  : void {
        parent::setUp();
        $this->artisan('migrate:fresh');

        $mock = Mockery::mock(AuthorizationService::class);
        $mock->shouldReceive('isServiceAvailable')->andReturn(True);

        $this->app->bind(AuthorizationService::class, function() use ($mock) {
            return $mock;
        });
    }
    

    public function testShopKeeperMustNotTransferMoney() {
        $payer =User::factory(1)->has(Wallet::factory())->create([
            'identifier' => '69850570000177',
            'user_type'  => 'shopkeeper',
        ])->first();
        
        $response = $this->actingAs($payer, 'api')->post('/api/transaction', ['payer' => $payer->id, 
                                         'payee' => 9999, 
                                         'amount' => 30]);
        
        $response->assertResponseStatus(400);
        $response->seeJsonEquals(["message" => "ShopKeepers can't transfer money!"]);
    }


    public function testTransactionShouldWorkAsExpected() {

        Queue::fake();

        $payee =User::factory(1)->has(Wallet::factory())->create([
            'identifier' => '69850570000177',
            'user_type'  => 'shopkeeper',
        ])->first();

        $payer =User::factory(1)->has(Wallet::factory())->create([
            'identifier' => '26791226072',
            'user_type'  => 'customer',
        ])->first();

        $payerCurrentAmount = $payer->wallet->balance;
        $payeeCurrentAmount = $payee->wallet->balance;
        $transferedAmount   = 50;

        $response = $this->actingAs($payer, 'api')->post('/api/transaction', ['payer' => $payer->id, 
        'payee' => $payee->id, 
        'amount' => $transferedAmount]);


        $response->assertResponseStatus(200);
        Queue::assertPushed(SendTransactionNotificationJob::class, 1);
        $this->assertEquals( $payer->wallet()->first()->balance, $payerCurrentAmount - $transferedAmount);
        $this->assertEquals( $payee->wallet()->first()->balance, $payeeCurrentAmount + $transferedAmount);

    }


}
