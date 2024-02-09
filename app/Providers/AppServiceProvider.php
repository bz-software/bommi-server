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
        $this->cpfValidator();
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
}
