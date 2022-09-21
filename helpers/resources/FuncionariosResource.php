<?php

namespace app\helpers\resources;

use app\models\Funcionarios;

class FuncionariosResource extends Funcionarios{
    public function fields(){
        return [
            "fun_id",
            "fun_nome",
            "fun_primeiro_nome",
            "fun_telefone",
            "fun_email",
            "fun_avatar",
        ];
    }
}