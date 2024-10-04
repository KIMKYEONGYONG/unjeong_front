<?php

namespace App\Helper;

use App\Core\Utility;
use App\DataObjects\UploadedFileData;
use App\Enum\Storage;
use App\Exception\ValidationException;
use Psr\Http\Message\UploadedFileInterface;
use Exception;

class UploadFileHelper
{
    public function __construct(
        private readonly Utility $utility
    ) {

    }

    public function moveUploadedFile(
        string $directory,
        UploadedFileInterface $uploadedFile,
        Storage $storage = Storage::Local,
    ) : ?UploadedFileData
    {
       try {
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {

                $filename = $this->utility->uniqueFileName($uploadedFile->getClientFilename());
                $path = UPLOAD_PATH . "/$directory";

                $this->utility->makeDir($path);

                $uploadFileFullPath = $path . DIRECTORY_SEPARATOR . $filename;
                $uploadedFile->moveTo($uploadFileFullPath);

                return new UploadedFileData(
                    $uploadedFile->getClientFilename(),
                    $filename,
                    $uploadedFile->getClientMediaType(),
                    $uploadedFile->getSize(),
                    $path,
                );
            }
        } catch (Exception | ValidationException $e) {
            error_log('upload error '.$e->getMessage());
        }
        return null;

    }

    public function removeFile(
        string $directory,
        string $fileName,
        Storage $storage = Storage::Local,
    ) : void
    {
        $path = UPLOAD_PATH . "/$directory";
        if(Storage::Local === $storage){
            $this->utility->removeFile($path, $fileName);
        }
    }

    public function removeDir(
        string $directory,
        Storage $storage = Storage::Local,
    ) : void
    {
        $path = UPLOAD_PATH . "/$directory";
        if(Storage::Local === $storage){
            $this->utility->removeDir($path);
        }
    }

}