<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->cpfValidator();
        $this->cnpj();
        $this->phoneNumber();
        JsonResource::withoutWrapping();
    }

    public function cpfValidator(){
        Validator::extend('cpf', function ($attribute, $value, $parameters, $validator) {
            // Remove non-numeric characters from CPF
            $cpf = preg_replace('/[^0-9]/', '', $value);

            // Check if the CPF has 11 digits
            if (strlen($cpf) !== 11) {
                return false;
            }

            // Check if all digits are the same (ex: 11111111111)
            if (preg_match('/(\d)\1{10}/', $cpf)) {
                return false;
            }

            // Check the first check digit
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum += intval($cpf[$i]) * (10 - $i);
            }
            $digit = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);
            if (intval($cpf[9]) !== $digit) {
                return false;
            }

            // Check the second check digit
            $sum = 0;
            for ($i = 0; $i < 10; $i++) {
                $sum += intval($cpf[$i]) * (11 - $i);
            }
            $digit = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);
            if (intval($cpf[10]) !== $digit) {
                return false;
            }

            return true;
        });

        Validator::replacer('cpf', function ($message, $attribute, $rule, $parameters) {
            return 'CPF inválido';
        });
    }

    public function phoneNumber(){
        Validator::extend('phonenumber', function ($attribute, $value, $parameters, $validator) {
            return (bool) preg_match('/[0-9]{10,11}/', preg_replace('/\D/', '', $value));
        });

        Validator::replacer('phonenumber', function ($message, $attribute, $rule, $parameters) {
            return 'Telefone Inválido';
        });
    }

    public function cnpj(){
        Validator::extend('cnpj', function ($attribute, $value, $parameters, $validator) {
            $cnpj = preg_replace('/[^0-9]/', '', (string) $value);
	
            // Valida tamanho
            if (strlen($cnpj) != 14)
                return false;

            // Verifica se todos os digitos são iguais
            if (preg_match('/(\d)\1{13}/', $cnpj))
                return false;	

            // Valida primeiro dígito verificador
            for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
            {
                $soma += $cnpj[$i] * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }

            $resto = $soma % 11;

            if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
                return false;

            // Valida segundo dígito verificador
            for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
            {
                $soma += $cnpj[$i] * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }

            $resto = $soma % 11;

            return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
        });

        Validator::replacer('cnpj', function ($message, $attribute, $rule, $parameters) {
            return 'CNPJ Inválido';
        });
    }
}
