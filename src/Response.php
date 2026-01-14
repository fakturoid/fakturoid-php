<?php

namespace Fakturoid;

use Fakturoid\Exception\InvalidResponseException;
use JsonException;
use Psr\Http\Message\ResponseInterface;

class Response
{
    private readonly int $statusCode;
    /** @var array<string, mixed> */
    private readonly array $headers;
    private readonly string $body;
    private readonly ResponseInterface $originalResponse;

    public function __construct(ResponseInterface $originalResponse)
    {
        $headers = [];
        foreach ($originalResponse->getHeaders() as $headerName => $value) {
            $headers[$headerName] = $originalResponse->getHeaderLine($headerName);
        }
        $this->statusCode = $originalResponse->getStatusCode();
        $this->headers = $headers;
        $this->body = $originalResponse->getBody()->getContents();
        $this->originalResponse = $originalResponse;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeader(string $name): ?string
    {
        foreach ($this->headers as $headerName => $value) {
            if (strtolower($headerName) === strtolower($name)) {
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
        return $contentType !== null && str_contains($contentType, 'application/json');
    }

    public function getRateLimitQuota(): ?int
    {
        $policy = $this->getHeader('X-RateLimit-Policy');
        if ($policy === null) {
            return null;
        }
        if (preg_match('/q=(\d+)/', $policy, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    public function getRateLimitWindow(): ?int
    {
        $policy = $this->getHeader('X-RateLimit-Policy');
        if ($policy === null) {
            return null;
        }
        if (preg_match('/w=(\d+)/', $policy, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    public function getRateLimitRemaining(): ?int
    {
        $rateLimit = $this->getHeader('X-RateLimit');
        if ($rateLimit === null) {
            return null;
        }
        if (preg_match('/r=(\d+)/', $rateLimit, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    public function getRateLimitReset(): ?int
    {
        $rateLimit = $this->getHeader('X-RateLimit');
        if ($rateLimit === null) {
            return null;
        }
        if (preg_match('/t=(\d+)/', $rateLimit, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    public function getOriginalResponse(): ResponseInterface
    {
        return $this->originalResponse;
    }
}
