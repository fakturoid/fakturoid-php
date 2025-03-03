<?php

namespace Fakturoid\Exception;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class RequestException extends Exception implements RequestExceptionInterface
{
    private RequestInterface $request;
    private ResponseInterface $response;

    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        ?Throwable $previous = null
    ) {
        $this->request = $request;
        $this->response = $response;
        parent::__construct($response->getReasonPhrase(), $response->getStatusCode(), $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
