<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Enum\ActionMode;
use App\Exception\ValidationException;
use App\Interfaces\RequestValidatorInterface;
use Valitron\Validator;

class LoginRequestValidator implements RequestValidatorInterface
{

    public function validate(array $data, ActionMode $mode = ActionMode::Reg, array $files =[]): array
    {
        $v = new Validator($data);
        $v->rule('required',['userId','password'])->labels(['userId'=>'아이디','password'=>'비밀번호']);

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}