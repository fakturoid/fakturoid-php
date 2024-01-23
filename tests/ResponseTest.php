<?php

namespace Fakturoid\Tests;

use Fakturoid\Response;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends TestCase
{
    public function testJson()
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

    public function testJsonWithMixedHeadersCase()
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

    public function testOther()
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
}
