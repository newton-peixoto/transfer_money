<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;

class AuthController extends Controller
{

    private $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function signUp(Request $request)
    {
        $this->validate($request, [
            'name'       => 'required|string',
            'email'      => 'required|email|unique:users',
            'identifier' => 'required|unique:users',
            'password'   => 'required|confirmed',
        ]);

        try {

            $userData = [
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => $request->password,
                'identifier' => $request->identifier
            ];

            $user = $this->userService->createUser($userData);

            return response()->json(['user' => $user, 'message' => 'User created!'], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }

    }

    public function signIn(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


}