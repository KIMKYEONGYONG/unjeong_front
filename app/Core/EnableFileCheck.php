<?php

declare(strict_types=1);

namespace App\Core;

use App\Exception\ValidationException;
use App\Traits\EnableFileTrait;
use Psr\Http\Message\UploadedFileInterface;

class EnableFileCheck
{
    use EnableFileTrait;
    public function __construct(private readonly Config $config)
    {
    }

    public function enableFile(
        ?UploadedFileInterface $uploadedFile,
        bool $isOnlyImage = false,
        string $emptyFileMessage = '파일 또는 등록하실 이미지를 선택하세요',
    ): void
    {
        if (!isset($uploadedFile)) {
            throw new ValidationException($emptyFileMessage);
        }
        if (!$this->isFileUploaded($uploadedFile)) {
            throw new ValidationException($emptyFileMessage);
        }
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        if ($isOnlyImage && !$this->isImageFile($uploadedFile)) {
            throw new ValidationException('이미지 파일만 가능합니다');
        }

        if ($this->denyFileExtensionCheck($extension)) {
            throw new ValidationException(sprintf('확장자 "%s" 는 허용되지 않습니다', $extension));
        }
        if (!$this->allowFileSize($uploadedFile)) {
            throw new ValidationException(
                sprintf(
                    '업로드 가능한 파일 최대 크기는 %dMb 입니다',
                    $this->config->get('upload.max_size')
                )
            );
        }
    }
}