<?php

declare(strict_types=1);

namespace App\Traits;

use App\Core\Config;
use Psr\Http\Message\UploadedFileInterface;

trait EnableFileTrait
{
    public function __construct(private readonly Config $config)
    {
    }

    public function denyFileExtensionCheck(string $extension): bool
    {
        $denyExtension = $this->config->get('upload.deny_extension');
        return in_array(strtolower($extension), $denyExtension, true);
    }

    public function allowFileSize(UploadedFileInterface $uploadedFile): bool
    {
        $maxFileSize = $this->config->get('upload.max_size') * 1024 * 1024;

        return !($uploadedFile->getSize() > $maxFileSize);
    }

    public function isFileUploaded(UploadedFileInterface $uploadedFile): bool
    {
        return $uploadedFile->getError() === UPLOAD_ERR_OK;
    }

    public function isImageFileCheckForServerLocalFile(string $filePath): bool
    {
        return $this->checkImageFile($filePath);
    }

    public function isImageFile(UploadedFileInterface $uploadedFile): bool
    {
        return $this->checkImageFile($uploadedFile->getStream()->getMetadata('uri'));
    }

    public function isZipFile(UploadedFileInterface $uploadedFile):bool
    {
        $zipTypes = ['application/zip'];
        $file = $uploadedFile->getStream()->getMetadata('uri');
        $mime = mime_content_type($file);
        if ($mime === false) {
            return false;
        }
        if (!in_array($mime,$zipTypes)) {
            return false;
        }
        return true;
    }

    private function checkImageFile(string $file): bool
    {
        $result = true;
        $imginfo = getimagesize($file);
        if (empty($imginfo)) {
            return false;
        }
        switch ($imginfo[2]) {
            case 1: // GIF
                if (!imagecreatefromgif($file)) {
                    $result = false;
                }
                break;
            case 2: // JPG
                if (!imagecreatefromjpeg($file)) {
                    $result = false;
                }
                break;
            case 3: // PNG
                if (!imagecreatefrompng($file)) {
                    $result = false;
                }
                break;
            case 6: // BMP
                if (!imagecreatefromwbmp($file)) {
                    $result = false;
                }
                break;
            default:
                $result = false;
        }
        return $result;
    }
}