<?php 


namespace App\Services;

use App\Jobs\SendTransactionNotificationJob;
use App\Models\User;
use App\Models\Transaction;
use GuzzleHttp\Client;
use DB;

class TransactionService {

    const BASE_URL = 'https://run.mocky.io/';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URL
        ]);
    }

    public function transfer(array $data) {

        if(!$this->isServiceAvailable()) {
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
        $wallet = User::find($payer)->first()->wallet;

        return $wallet->balance >= $amount;
    }


    private function isServiceAvailable() {
        
        $uri = '/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
        try {
            $response = $this->client->request('GET',$uri);
            return json_decode($response->getBody(), true);
        } catch (\GuzzleException $e) {
            return ['message' => 'Não Autorizado'];
        }
    }

}