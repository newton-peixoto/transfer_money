<?php 


namespace App\Helpers;

use App\Exceptions\AuthException;

class Identifier {


    const CUSTOMER   = 'customer';
    const SHOPKEEPER = 'shopkeeper';

    private $value, $userType;
    

    public function __construct($value) {
        $this->validateIdentifier($value);
        $this->value      = $value;
    }


    private function validateIdentifier($value) {

        switch ($value) {
            case $this->validateCPF($value):
                $this->setUserType(self::CUSTOMER);
                break;
            case $this->validateCNPJ($value):
                $this->setUserType(self::SHOPKEEPER);
                break;
            default:
                throw new AuthException("Identifier not valid! Please check again.", 400);
                break;
        }
    }

    private function validateCNPJ($cnpj) {
        // Verificar se foi informado
        if(empty($cnpj))
        return false;

        // Remover caracteres especias
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Verifica se o numero de digitos informados
        if (strlen($cnpj) != 14)
            return false;

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;

        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0, $n = 0; $i < 12; $n += $cnpj[$i] * $b[++$i]);

        if ($cnpj[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for ($i = 0, $n = 0; $i <= 12; $n += $cnpj[$i] * $b[$i++]);

        if ($cnpj[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }

    private function validateCPF($cpf) {
        // Verificar se foi informado
        if(empty($cpf))
        return false;

        // Remover caracteres especias
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se o numero de digitos informados
        if (strlen($cpf) != 11)
            return false;

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf))
            return false;

        // Calcula os digitos verificadores para verificar se o
        // CPF é válido
        for ($t = 9; $t < 11; $t++) {

            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }

            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }


    public function getValue() {
        return $this->value;
    }

    public function getUserType() {
        return $this->userType;
    }

    private function setUserType($value) {
        $this->userType = $value;
    }
}