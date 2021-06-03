<?php

use App\Models\User;

class UserControllerTest extends TestCase
{

    public function setUp()  : void {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }


    public function testGivenValidDataShouldRegisterUser(){
        $this->post('/api/signUp', [
            'name'                    => 'John',
            'password'                => '123123',
            'password_confirmation'   => '123123',
            'email'                   => 'teste@teste.com',
            'identifier'              => '26791226072'
        ])->seeJsonContains(['message' => 'User created!']);
    }


    public function testGivenInvalidDataShouldNotRegisterUser(){
        $this->post('/api/signUp', [
            'name'                    => 'John',
            'password'                => '123123',
            'password_confirmation'   => '123123',
            'email'                   => 'teste@teste.com',
            'identifier'              => '2679122607'
        ])->seeJsonContains(['message' => 'Identifier not valid! Please check again.']);
    }

    public function testShouldReturnTokenIfUserTriesToSignIn() {

        User::factory()->create(['email' => 'teste@teste.com.br']);

        $this->post('/api/signIn', [
            'email' => 'teste@teste.com.br',
            'password' => '123123'
        ])->seeJsonStructure(['token', 'token_type', 'expires_in']);
    }


    public function testShouldReturnUnauthorizedIfUserNotValid() {

        $this->post('/api/signIn', [
            'email' => 'invalid@teste.com.br',
            'password' => '123123'
        ])->assertResponseStatus(401);
    }

 
}
