<?php

declare(strict_types=1);

namespace App\Handlers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\ResponseEmitter;

class ShutdownHandler
{
    public function __construct(
        private readonly Request $request,
        private readonly HttpErrorHandler $errorHandler,
        private readonly bool $displayErrorDetails
    ) {
    }

    public function __invoke(): void
    {
        $error = error_get_last();
        if ($error) {
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorMessage = $error['message'];
            $errorType = $error['type'];
            $message = 'An error while processing your request. Please try again later.';

            if ($this->displayErrorDetails) {
                switch ($errorType) {
                    case E_ERROR:
                    case E_USER_ERROR:
                        $message = "FATAL ERROR: $errorMessage. ";
                        $message .= " on line $errorLine in file $errorFile.";
                        break;
                    case E_WARNING:
                    case E_USER_WARNING:
                        $message = "WARNING: $errorMessage";
                        break;
                    case E_NOTICE:
                    case E_USER_NOTICE:
                        $message = "NOTICE: $errorMessage";
                        break;

                    default:
                        $message = "ERROR: $errorMessage";
                        $message .= " on line $errorLine in file $errorFile.";
                        break;
                }
            }
            $exception = new HttpInternalServerErrorException($this->request, $message);
            $response = $this->errorHandler->__invoke(
                $this->request,
                $exception,
                $this->displayErrorDetails,
                false,
                false
            );

            if (ob_get_length()) {
                ob_clean();
            }

            $responseEmitter = new ResponseEmitter();
            $responseEmitter->emit($response);
        }
    }
}