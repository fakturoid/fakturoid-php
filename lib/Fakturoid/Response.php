<?php

namespace Fakturoid;

class Response
{
    private $statusCode;
    private $headers;
    private $body;

    public function __construct($info, $response)
    {
        $this->statusCode = $info['http_code'];
        $this->headers    = $info['headers'];

        if ($this->isJson()) {
            $this->body = json_decode($response);
        } else {
            $this->body = $response;
        }
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeader($name)
    {
        foreach ($this->headers as $headerName => $value) {
            if (strtolower($headerName) == strtolower($name)) {
                return $value;
            }
        }
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getBody()
    {
        // Typically in 304 Not Modified.
        if ($this->body === '') {
            return null;
        }

        return $this->body;
    }

    private function isJson()
    {
        if (empty($this->getHeader('Content-Type'))) {
            return false;
        }

        $contentType = $this->getHeader('Content-Type');
        return strpos($contentType, 'application/json') !== false;
    }
}
