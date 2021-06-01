<?php

use App\Services\UserService;

class UserServiceTest extends TestCase
{

    public function setUp()  : void {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    public function testGivenValidDataShouldCreateUser(){
        $data = ['name' => 'tester', 
                'email' => 'tester@email.com', 
                'password' => '123123',
                'identifier' => '26791226072'];
        $userService = new UserService;
        $user = $userService->createUser($data);

        $this->assertEquals($user->email, $data['email']);
    }

    public function testGivenInvalidDataShouldNotCreateUser(){
        $this->expectExceptionMessage('Error while creating user in the database!');
        $data = ['name' => 'tester', 
                'email' => 'tester@email.com', 
                'identifier' => '26791226072'];
        $userService = new UserService;
        $user = $userService->createUser($data);
    }
 
}
