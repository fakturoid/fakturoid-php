<?php

namespace Fakturoid\Exception;

use Fakturoid\Response;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

class RequestException extends Exception implements RequestExceptionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly Response $response,
        ?Throwable $previous = null
    ) {
        parent::__construct($response->getOriginalResponse()->getReasonPhrase(), $response->getStatusCode(), $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
