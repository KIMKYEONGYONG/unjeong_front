<?php

namespace App\Core;

use RuntimeException;

class Utility
{
    public function __construct()
    {
    }

    public function getGuid(): string
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(16384, 20479),
            random_int(32768, 49151),
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(0, 65535)
        );
    }

    public function makeDir($path):void
    {
        if(!is_dir($path)) {
            umask(0);
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
    }

    public function uniqueFileName(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $basename = $this->getGuid();
        return sprintf('%s.%0.8s', $basename, $extension);
    }

    public function removeDir(string $delete_path): void
    {
        if (@file_exists($delete_path)) {
            $dirs = @dir($delete_path);
            while(false !== ($entry = $dirs->read())) {
                if(($entry !== '.') && ($entry !== '..')) {
                    if(@is_dir($delete_path.'/'.$entry)) {
                        self::removeDir($delete_path.'/'.$entry);
                    }
                    else {
                        @unlink($delete_path.'/'.$entry);
                    }
                }
            }
            $dirs->close();
            @rmdir($delete_path);
        }
    }

    public function removeFile(string $filePath, string $fileName): void
    {
        $deleteFilePath = $filePath.$fileName;
        if (@file_exists($deleteFilePath)) {
            if (!unlink($deleteFilePath)) {
                throw new RuntimeException('unlink Failed!');
            }
        }
    }

    public function copyFile(string $file, string $newFile): void
    {
        if (@file_exists($file)) {
            if (!copy($file, $newFile)) {
                throw new RuntimeException('Copy Failed!');
            }
        }
    }

    public function nameFilter(string $name , int $endPoint) : string
    {
        $nameLength = mb_strlen($name, 'UTF-8');
        $filteredName = mb_substr($name, 0, 1, 'UTF-8'); // 첫 글자는 그대로 유지
        for ($i = 1; $i < $nameLength; $i++) {
            $filteredName .= '*';
        }
        return $filteredName;
    }

    public function phoneFilter(string $phone, int $endPoint) : string
    {
        $phoneParts = explode('-', $phone);
        $filteredPhone = $phoneParts[0] . '-';
        $filteredPhone .= str_repeat('*', $endPoint) . '-';
        $filteredPhone .= substr($phoneParts[2], -1 * $endPoint);
        return $filteredPhone;
    }

}