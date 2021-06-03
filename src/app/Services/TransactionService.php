<?php 


namespace App\Services;

use App\Jobs\SendTransactionNotificationJob;
use App\Models\User;
use App\Models\Transaction;
use App\Services\AuthorizationService;
use DB;

class TransactionService {

    const BASE_URL = 'https://run.mocky.io/';

    private $authorizationService;

    public function __construct(AuthorizationService $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    public function transfer(array $data) {

            
        if(!$this->authorizationService->isServiceAvailable()) {
            throw new \Exception('Service is not available. Try again later.');     
        } 

        if(!$this->checkUserBalance($data['payer'], $data['amount'])) {
            throw new \Exception("The requested amount is not available.", 422);
        }

        $this->makeTransaction($data);
    }

    public function makeTransaction($data) {
        $transactionData = [
            'payer_id' => $data['payer'],
            'payee_id' => $data['payee'],
            'amount' => $data['amount']
        ];

        try {

            DB::beginTransaction();
                $transaction = Transaction::create($transactionData);   
            
                $transaction->payer->wallet->withDraw($transactionData['amount']);
                $transaction->payee->wallet->deposit($transactionData['amount']);

            DB::commit();

            dispatch(new SendTransactionNotificationJob)->onQueue('transaction_notifications');

        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception("Error while making transaction!");
        }
    }

    private function checkUserBalance($payer, $amount) {
        $wallet = User::where('id', $payer)->first()->wallet;
        
        return $wallet->balance >= $amount;
    }


}