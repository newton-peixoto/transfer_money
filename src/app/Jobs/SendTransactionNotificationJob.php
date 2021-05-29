<?php

namespace App\Jobs;

use App\Services\TransactionNotifierService;

class SendTransactionNotificationJob extends Job
{
    public function handle()
    {
        TransactionNotifierService::sendNotification();
    }
}
