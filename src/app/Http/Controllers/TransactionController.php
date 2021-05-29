<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\TransactionService;

class TransactionController extends Controller
{

    public function __construct(TransactionService $transactionService)
    {
        $this->middleware('auth');
        $this->middleware('allowed_transfer');

        $this->transactionService = $transactionService;
    }

    public function transaction(Request $request) {


        $this->validate($request, [
            'payee'       => 'required',
            'amount'      => 'required|numeric|gt:0'
        ]);

        try {
            $userId = \Auth::user()->id;
            
            $data = ['payee' => $request->payee, 'payer' => $userId, 'amount' => $request->amount ];

            $this->transactionService->transfer($data);

            return response()->json(['message' => 'The money has been transfered'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() == 0 ? 500 : $e->getCode());
        }

    }

}