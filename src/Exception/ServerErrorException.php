<?php

namespace Fakturoid\Exception;

use Fakturoid\Response;

class ServerErrorException extends BadResponseException
{
    public static function createException(Response $response): self
    {
        $message = match ($response->getStatusCode()) {
            503 => 'Fakturoid is in read only state',
            default => 'Server error',
        };
        $exception = new self($message, $response->getStatusCode());
        $exception->setResponse($response);
        return $exception;
    }
}
