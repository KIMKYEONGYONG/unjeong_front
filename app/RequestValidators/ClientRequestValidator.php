<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Core\JsonFormatter;
use App\Enum\ActionMode;
use App\Exception\ValidationException;
use App\Interfaces\RequestValidatorInterface;
use Valitron\Validator;

class ClientRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data, ActionMode $mode = ActionMode::Reg, array $files =[]): array
    {
        $v = new Validator($data);

        error_log("test data = ".JsonFormatter::encode($data));

        $v->rule('required','phone1')->message('휴대폰 번호 앞자리를 선택해주세요.');
        $v->rule('required','age')->message('연령대를 선택해주세요.');
        $v->rule('required','gender')->message('성별을 선택해주세요.');
        $v->rule('required','term1')->message('개인정보수집 및 이용동의에 동의해주세요.');
        $v->rule('required','term2')->message('개인정보 위탁에 대해서 동의해주세요');

        $rules =  [
            'name' => ['required',['lengthMax', 10]],
            'phone1' => ['required',['lengthMax', 3]],
            'phone2' => ['required',['lengthMax', 4]],
            'phone3' => ['required',['lengthMax', 4]],
            'age' => [['lengthMax', 5],'numeric'],
            'gender' => [['lengthMax', 5]],
            'addr' => ['required',['lengthMax', 255]],
        ];

        $v->mapFieldsRules($rules);


        $v->labels(
            [
                'name' => '성명',
                'age' => '연령대',
                'gender' => '성별',
                'phone1' => '휴대폰번호',
                'phone2' => '휴대폰번호',
                'phone3' => '휴대폰번호',
                'addr' => '주소',
            ]
        );

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        $phone = $data['phone1']. '-' . $data['phone2'] . '-'.  $data['phone3'];
        return $data + ['phone' => $phone];
    }
}