<?php

namespace Fakturoid\Tests;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function createPsrResponseMock(int $statusCode, string $contentType, string $body): ResponseInterface
    {
        $responseInterface = $this->createMock(ResponseInterface::class);
        $responseInterface
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn($statusCode);
        $responseInterface
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['content-type' => [$contentType]]);
        $responseInterface
            ->expects($this->once())
            ->method('getHeaderLine')
            ->willReturn($contentType);
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock($body));

        return $responseInterface;
    }

    protected function getStreamMock(string $content): StreamInterface
    {
        return new class ($content) implements StreamInterface
        {
            public function __construct(
                private readonly string $content
            ) {
            }

            public function __toString(): string
            {
                return $this->getContents();
            }

            public function close(): void
            {
            }

            public function detach()
            {
            }

            public function getSize(): ?int
            {
                return 0;
            }

            public function tell(): int
            {
                return 0;
            }

            public function eof(): bool
            {
                return false;
            }

            public function isSeekable(): bool
            {
                return false;
            }

            public function seek(int $offset, int $whence = SEEK_SET): void
            {
            }

            public function rewind(): void
            {
            }

            public function isWritable(): bool
            {
                return false;
            }

            public function write(string $string): int
            {
                return 0;
            }

            public function isReadable(): bool
            {
                return true;
            }

            public function read(int $length): string
            {
                return '';
            }

            public function getMetadata(?string $key = null)
            {
            }

            public function getContents(): string
            {
                return $this->content;
            }
        };
    }
}
