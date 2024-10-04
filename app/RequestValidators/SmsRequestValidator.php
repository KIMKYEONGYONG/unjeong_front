<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Enum\ActionMode;
use App\Exception\ValidationException;
use App\Interfaces\RequestValidatorInterface;
use Valitron\Validator;

class SmsRequestValidator implements RequestValidatorInterface
{

    public function validate(array $data, ActionMode $mode = ActionMode::Reg, array $files =[]): array
    {
        $v = new Validator($data);

        $v->rule('required','phone1')->message('휴대전화 앞자리를 선택하세요');


        $rules =  [
            'name' => ['required',['lengthMax', 50]],
            'phone1' => ['required',['lengthMax', 3], 'numeric'],
            'phone2' => ['required',['lengthMax', 4], 'numeric'],
            'phone3' => ['required',['lengthMax', 4], 'numeric'],
        ];

        $v->mapFieldsRules($rules);


        $v->labels(
            [
                'name' => '성명',
                'phone1' => '휴대전화',
                'phone2' => '휴대전화',
                'phone3' => '휴대전화',
            ]
        );


        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        $data['phone'] = ($data['phone1'].'-'.$data['phone2'].'-'.$data['phone3']);

        return $data;
    }
}