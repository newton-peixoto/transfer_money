<?php 


namespace App\Services;


use GuzzleHttp\Client;


class AuthorizationService {

    const BASE_URL = 'https://run.mocky.io/';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URL
        ]);
    }

    public function isServiceAvailable() {
        
        $uri = '/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
        try {
            $response = $this->client->request('GET',$uri);
            $response = json_decode($response->getBody(), true);
            
            return $response['message'] == 'Autorizado';
        } catch (\Exception $e) {
            return false;
        }
    }

}