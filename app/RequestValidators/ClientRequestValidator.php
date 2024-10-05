<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Enum\ActionMode;
use App\Exception\ValidationException;
use App\Interfaces\RequestValidatorInterface;
use Valitron\Validator;

class ClientRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data, ActionMode $mode = ActionMode::Reg, array $files =[]): array
    {
        $v = new Validator($data);


        $rules =  [
            'name' => ['required',['lengthMax', 10]],
            'phone' => ['required',['lengthMax', 13],'cellphone'],
            'age' => ['required',['lengthMax', 5],'numeric'],
            'gender' => ['required',['lengthMax', 1]],
            'addr' => ['required',['lengthMax', 255]],
        ];

        $v->mapFieldsRules($rules);


        $v->labels(
            [
                'name' => '성명',
                'phone' => '휴대폰번호',
                'age' => '연령대',
                'gender' => '성별',
                'addr' => '주소',
            ]
        );

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}