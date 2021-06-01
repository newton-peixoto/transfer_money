<?php

use App\Helpers\Identifier;

class IdentifierTest extends TestCase
{

    public function testGivenValidCPFShouldReturnIdentifierAsCustomer(){
        $identifier = new Identifier("26791226072");

        $this->assertInstanceOf(Identifier::class, $identifier);
        $this->assertEquals($identifier->getUserType(), 'customer');
    }

    public function testGivenValidCNPJShouldReturnIdentifierAsShopkeeper(){
        $identifier = new Identifier("69850570000177");

        $this->assertInstanceOf(Identifier::class, $identifier);
        $this->assertEquals($identifier->getUserType(), 'shopkeeper');
    }


    public function testGivenInvalidCPFShouldThrowException(){
        $this->expectExceptionMessage('Identifier not valid! Please check again.');
        $identifier = new Identifier("2679226072");
    }

    public function testGivenInvalidCNPJShouldThrowException(){
        $this->expectExceptionMessage('Identifier not valid! Please check again.');
        $identifier = new Identifier("6985057000177");
    }
 
}
