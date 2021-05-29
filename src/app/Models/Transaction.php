<?php 

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $fillable = [
        'id',
        'payee_id',
        'payer_id',
        'amount'
    ];


    public function payee()
    {
        return $this->belongsTo(User::class,  'payee_id');
    }

    public function payer()
    {
        return $this->belongsTo(User::class,  'payer_id');
    }

}