<?php

namespace Fakturoid;

use Fakturoid\Exception\InvalidResponseException;
use JsonException;
use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * @readonly
     */
    private int $statusCode;
    /** @var array<string, mixed>
     * @readonly
     */
    private array $headers;
    /**
     * @readonly
     */
    private string $body;

    public function __construct(ResponseInterface $response)
    {
        $headers = [];
        foreach ($response->getHeaders() as $headerName => $value) {
            $headers[$headerName] = $response->getHeaderLine($headerName);
        }
        $this->statusCode = $response->getStatusCode();
        $this->headers = $headers;
        $this->body = $response->getBody()->getContents();
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeader(string $name): ?string
    {
        foreach ($this->headers as $headerName => $value) {
            if (strtolower($headerName) == strtolower($name)) {
                return $value;
            }
        }
        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return string|array<string, mixed>|\stdClass|null
     * @throws InvalidResponseException
     */
    public function getBody(bool $returnJsonAsArray = false)
    {
        // Typically in 304 Not Modified.
        if ($this->body === '') {
            return null;
        }

        if (!$this->isJson()) {
            return $this->body;
        }

        try {
            $json = json_decode($this->body, $returnJsonAsArray, 512, JSON_THROW_ON_ERROR);
            if ($json === false) {
                throw new InvalidResponseException('Invalid JSON response');
            }
            return $json;
        } catch (JsonException $exception) {
            throw new InvalidResponseException('Invalid JSON response', $exception->getCode(), $exception);
        }
    }

    private function isJson(): bool
    {
        $contentType = $this->getHeader('Content-Type');
        return $contentType !== null && strpos($contentType, 'application/json') !== false;
    }
}
