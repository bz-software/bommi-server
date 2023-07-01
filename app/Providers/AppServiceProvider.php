<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        $this->fullNameValidator();
        $this->cpfValidator();
    }

    private function fullNameValidator(){
        Validator::extend('full_name', function ($attribute, $value, $parameters, $validator) {
            $names = explode(' ', $value);
            return count($names) >= 2;
        });
    
        Validator::replacer('full_name', function ($message, $attribute, $rule, $parameters) {
            return 'O nome deve ser composto por nome e sobrenome.';
        });
    }

    private function cpfValidator(){
        Validator::extend('cpf', function ($attribute, $value, $parameters, $validator) {
            // Remover caracteres não numéricos do CPF
            $cpf = preg_replace('/[^0-9]/', '', $value);
            
            // Verificar se o CPF possui 11 dígitos
            if (strlen($cpf) !== 11) {
                return false;
            }
            
            // Verificar se todos os dígitos são iguais (ex: 11111111111)
            if (preg_match('/(\d)\1{10}/', $cpf)) {
                return false;
            }
            
            // Verificar o primeiro dígito verificador
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum += intval($cpf[$i]) * (10 - $i);
            }
            $digit = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);
            if (intval($cpf[9]) !== $digit) {
                return false;
            }
            
            // Verificar o segundo dígito verificador
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
}
