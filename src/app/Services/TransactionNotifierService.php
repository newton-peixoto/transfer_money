<?php


namespace App\Services;

use GuzzleHttp\Client;

class TransactionNotifierService {

    const BASE_URL = 'https://run.mocky.io/';

    public static function sendNotification() {

        $uri = 'v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04';
        try {
            $client = new Client([
                'base_uri' => self::BASE_URL
            ]);

            $response = $client->request($uri);

            return json_decode($response->getBody(), true);
        } catch (\Exception $exception) {
            throw new \Exception("Error, retry job!");
            
        }
    }

}