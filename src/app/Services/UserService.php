<?php 


namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Helpers\Identifier;
use App\Exceptions\AuthException;
use DB;

class UserService {

    public function createUser(array $userData) {

        $identifier       = new Identifier($userData['identifier']);

        try {

            DB::beginTransaction();

            $user             = new User;
            $user->name       = $userData['name'];
            $user->email      = $userData['email'];
            $plainPassword    = $userData['password'];
            $user->password   = app('hash')->make($plainPassword);
            $user->identifier = $identifier->getvalue();
            $user->user_type  = $identifier->getUserType();
    
            $user->save();

            $wallet          = new Wallet;
            $wallet->user_id = $user->id;
            $wallet->balance = env('INITIAL_BALANCE', 100);

            $wallet->save();

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw new AuthException("Error while creating user in the database!", 400);
        }
    }


}