<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Core\EnableFileCheck;
use App\Enum\ActionMode;
use App\Enum\Board\BoardType;
use App\Exception\ValidationException;
use App\Interfaces\RequestValidatorInterface;
use Doctrine\ORM\Exception\NotSupported;
use Psr\Http\Message\UploadedFileInterface;
use Valitron\Validator;

class BoardRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EnableFileCheck $fileCheck,

    ) {}

    /**
     * @throws NotSupported
     */
    public function validate(array $data, ActionMode $mode = ActionMode::Reg, array $files =[]): array
    {
        $code = $data['boardType'];
        $boardType = BoardType::tryFrom($code);
        if($boardType === null){
            throw new ValidationException("잘못된 접근입니다.");
        }

        $v = new Validator($data);
        $v->rule('required','status')->message('상태를 선택해주세요.');
        $statusArray = match($mode){
            ActionMode::Reg => [100,900],
            ActionMode::Edit => [100,900,999],
            default => []
        };
        $v->rule('in', 'status', $statusArray)->message('상태를 선택해주세요.');


        $rules =  [
            'subject' => ['required',['lengthMax', 255]],
            'content' => [['lengthMax', 4294967295]],
        ];

        if($boardType === BoardType::Notice) {
            $rules['content'] = ['required', ['lengthMax', 4294967295]];
        }

        if($boardType !== BoardType::Notice) {
            $rules['linkUrl'] = ['required', ['lengthMax', 255]];
            $rules['mediaName'] = [['lengthMax', 100]];
        }

        $v->mapFieldsRules($rules);


        $v->labels(
            [
                'subject' => '제목',
                'content' => '내용',
                'linkUrl' => match ($boardType) {
                    BoardType::PromotionalVideo => '동영상 주소',
                    BoardType::News => '링크 주소',
                    default => '',
                },
                'mediaName' => '언론사',
            ]
        );

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        if(!empty($files['file'])){
            /** @var UploadedFileInterface[] $dataFiles */
            $dataFile =  $files['file'];
            $fileCheck = !empty($dataFile->getClientFilename() );
            if ($fileCheck) {
                $this->fileCheck->enableFile($dataFile,false,'첨부파일을 선택하세요.');
                $data['file'] = $dataFile;
            }
        }

        if(!empty($files['thumbnailFile'])){
            /** @var UploadedFileInterface[] $dataFiles */
            $dataFile =  $files['thumbnailFile'];
            $fileCheck = !empty($dataFile->getClientFilename() );
            if ($fileCheck) {
                $this->fileCheck->enableFile($dataFile,true,'썸네일 파일을 선택하세요.');
                $data['thumbnailFile'] = $dataFile;
            }
        }

        return $data;
    }
}