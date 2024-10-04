<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Exception\ValidationException;
use App\Helper\ReadFileHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class ReadFileController extends Controller
{

    public function readFile(Request $request, Response $response): Response
    {
        $code = $request->getQueryParams()['code'] ?? '';
        if(empty($code)){
            return $response->withStatus(404);
        }

        $_filePath = ReadFileHelper::getFilePath($code);
        $fileDir = $_filePath['dir'] ?? '';
        $orgName = $_filePath['orgName'] ?? '';
        $fileName = $_filePath['fileName'] ?? '';

        $filePath = UPLOAD_PATH.'/'.$fileDir.'/'.$fileName;

        // 파일 존재 여부 및 파일인지 확인
        if (file_exists($filePath) && is_file($filePath)) {
            // 파일의 MIME 타입을 가져오는 함수 정의
            function getMimeType($filePath): bool|string
            {
                $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($fileInfo, $filePath);
                finfo_close($fileInfo);
                return $mimeType;
            }

            // 이미지의 MIME 타입을 가져와 콘텐츠 타입으로 설정
            $mimeType = getMimeType($filePath);

            // 브라우저에서 지원하지 않는 MIME 타입인 경우, 기본적으로 다운로드로 설정
            if ($mimeType === 'application/x-hwp') {
                header('Content-Type: application/force-download');
            } else {
                // 브라우저에서 지원하지 않는 MIME 타입인 경우, 기본적으로 다운로드로 설정
                if ($mimeType === 'application/octet-stream') {
                    $mimeType = 'application/force-download';
                }

                header('Content-Type: ' . $mimeType);
            }
            $_filePath = $filePath;
            if(!empty($orgName)){
                $_filePath = UPLOAD_PATH.$fileDir.'/'.$orgName;
            }
            header('Content-Disposition: attachment; filename="' . basename($_filePath) . '"');

            header('Content-Type: ' . $mimeType);

            // 이미지를 읽어와 브라우저에 출력
            readfile($filePath);
            return $response;
        }

        return $response->withStatus(404);
    }
}