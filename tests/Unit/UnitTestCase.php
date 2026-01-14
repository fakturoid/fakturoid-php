<?php

namespace Fakturoid\Tests\Unit;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

abstract class UnitTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array<string, array<string>> $headers
     */
    protected function createPsrResponseMock(
        int $statusCode,
        string $contentType,
        string $body,
        array $headers = []
    ): ResponseInterface {
        $responseInterface = $this->createMock(ResponseInterface::class);
        $responseInterface
            ->method('getStatusCode')
            ->willReturn($statusCode);

        // Merge content-type with custom headers
        $allHeaders = array_merge(['content-type' => [$contentType]], $headers);

        $responseInterface
            ->method('getHeaders')
            ->willReturn($allHeaders);

        // Configure getHeaderLine to return proper values
        $responseInterface
            ->method('getHeaderLine')
            ->willReturnCallback(function (string $name) use ($allHeaders, $contentType): string {
                $lowerName = strtolower($name);
                foreach ($allHeaders as $headerName => $values) {
                    if (strtolower($headerName) === $lowerName) {
                        return implode(', ', $values);
                    }
                }
                return $lowerName === 'content-type' ? $contentType : '';
            });

        $responseInterface
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
                return null;
            }

            public function getSize(): int
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
