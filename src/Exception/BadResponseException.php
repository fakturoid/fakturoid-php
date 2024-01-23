<?php

namespace Fakturoid\Exception;

use Fakturoid\Response;

class BadResponseException extends ConnectionFailedException
{
    private Response $response;

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
