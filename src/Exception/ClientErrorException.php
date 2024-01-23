<?php

namespace Fakturoid\Exception;

use Fakturoid\Response;

class ClientErrorException extends BadResponseException
{
    public static function createException(Response $response): self
    {
        $message = match ($response->getStatusCode()) {
            400 => 'Page not found',
            401 => 'Unauthorized',
            402 => 'Payment required or account is blocked',
            403 => 'Forbidden',
            404 => 'Record not found',
            415 => 'Unsupported media type',
            422 => 'Unprocessable entity',
            429 => 'Too many requests',
            default => 'Client error',
        };
        $exception = new self($message, $response->getStatusCode());
        $exception->setResponse($response);
        return $exception;
    }
}
