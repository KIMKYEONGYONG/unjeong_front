<?php

declare(strict_types=1);

namespace App\DataObjects;


class UploadedFileData
{
    public function __construct(
        public readonly string $clientName,
        public readonly string $uploadedFileName,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly string $uploadPath,
    ) {
    }
}