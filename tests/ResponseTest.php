<?php

namespace Fakturoid\Tests;

use Fakturoid\Response;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends TestCase
{
    public function testJson(): void
    {
        $responseInterface = $this->createMock(ResponseInterface::class);
        $responseInterface
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $responseInterface
            ->expects($this->once())
            ->method('getHeaderLine')
            ->willReturn('application/json; charset=utf-8');
        $responseInterface
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['content-type' => ['application/json; charset=utf-8']]);
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock('{"name":"Test"}'));
        $response = new Response($responseInterface);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals((object) ['name' => 'Test'], $response->getBody());
    }

    public function testJsonWithMixedHeadersCase(): void
    {
        $responseInterface = $this->createMock(ResponseInterface::class);
        $responseInterface
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $responseInterface
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['content-type' => ['application/json; charset=utf-8']]);
        $responseInterface
            ->expects($this->once())
            ->method('getHeaderLine')
            ->willReturn('application/json; charset=utf-8');
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock('{"name":"Test"}'));
        $response = new Response($responseInterface);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json; charset=utf-8', $response->getHeader('Content-Type'));
        $this->assertEquals('application/json; charset=utf-8', $response->getHeader('content-type'));
        $this->assertEquals('application/json; charset=utf-8', $response->getHeader('cOnTeNt-TyPe'));
        $this->assertEquals((object) ['name' => 'Test'], $response->getBody());
    }

    public function testOther(): void
    {
        $responseInterface = $this->createMock(ResponseInterface::class);
        $responseInterface
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock('Test'));

        $response = new Response($responseInterface);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([], $response->getHeaders());
        $this->assertNull($response->getHeader('Content-Type'));
        $this->assertEquals('Test', $response->getBody());
    }

    public function testRateLimitHeaders(): void
    {
        $responseInterface = $this->createMock(ResponseInterface::class);
        $responseInterface
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $responseInterface
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn([
                'X-RateLimit-Policy' => ['default;q=400;w=60'],
                'X-RateLimit' => ['default;r=398;t=55']
            ]);
        $responseInterface
            ->expects($this->exactly(2))
            ->method('getHeaderLine')
            ->willReturnMap([
                ['X-RateLimit-Policy', 'default;q=400;w=60'],
                ['X-RateLimit', 'default;r=398;t=55']
            ]);
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock(''));

        $response = new Response($responseInterface);

        $this->assertEquals(400, $response->getRateLimitQuota());
        $this->assertEquals(60, $response->getRateLimitWindow());
        $this->assertEquals(398, $response->getRateLimitRemaining());
        $this->assertEquals(55, $response->getRateLimitReset());
        $this->assertFalse($response->isRateLimitExceeded());
    }

    public function testRateLimitExceeded(): void
    {
        $responseInterface = $this->createMock(ResponseInterface::class);
        $responseInterface
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(429);
        $responseInterface
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn([
                'X-RateLimit-Policy' => ['default;q=400;w=60'],
                'X-RateLimit' => ['default;r=0;t=45']
            ]);
        $responseInterface
            ->expects($this->exactly(2))
            ->method('getHeaderLine')
            ->willReturnMap([
                ['X-RateLimit-Policy', 'default;q=400;w=60'],
                ['X-RateLimit', 'default;r=0;t=45']
            ]);
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock(''));

        $response = new Response($responseInterface);

        $this->assertEquals(429, $response->getStatusCode());
        $this->assertEquals(0, $response->getRateLimitRemaining());
        $this->assertEquals(45, $response->getRateLimitReset());
        $this->assertTrue($response->isRateLimitExceeded());
    }

    public function testRateLimitHeadersNotPresent(): void
    {
        $responseInterface = $this->createMock(ResponseInterface::class);
        $responseInterface
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $responseInterface
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn([]);
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock(''));

        $response = new Response($responseInterface);

        $this->assertNull($response->getRateLimitQuota());
        $this->assertNull($response->getRateLimitWindow());
        $this->assertNull($response->getRateLimitRemaining());
        $this->assertNull($response->getRateLimitReset());
        $this->assertFalse($response->isRateLimitExceeded());
    }
}
