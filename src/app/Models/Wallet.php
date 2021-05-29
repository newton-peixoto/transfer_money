<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model {
    
    protected $fillable = [
        'id', 'user_id', 'balance'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function withDraw($amount) {
        $this->update([
            'balance' => $this->balance - $amount
        ]);
    }

    public function deposit($amount) {
        $this->update([
            'balance' => $this->balance + $amount
        ]);
    }
}