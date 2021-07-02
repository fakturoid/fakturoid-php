<?php

declare(strict_types=1);

namespace fakturoid\fakturoid_php\Test;

use fakturoid\fakturoid_php\Response as Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testJson()
    {
        $headers = [
            'ETag'          => 'W/"e79a1fdf3cf010530b6d6827549915ce"',
            'Last-Modified' => 'Fri, 23 Mar 2018 14:57:17 GMT',
            'Content-Type'  => 'application/json; charset=utf-8'
        ];
        $response = new Response(
            [
                'http_code' => 200,
                'headers'   => $headers
            ],
            '{"name":"Test"}'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($headers, $response->getHeaders());
        $this->assertEquals('Fri, 23 Mar 2018 14:57:17 GMT', $response->getHeader('Last-Modified'));
        $this->assertEquals((object)['name' => 'Test'], $response->getBody());
    }

    public function testJsonWithMixedHeadersCase()
    {
        $headers = [
            'etag'          => 'W/"e79a1fdf3cf010530b6d6827549915ce"',
            'last-modified' => 'Fri, 23 Mar 2018 14:57:17 GMT',
            'content-type'  => 'application/json; charset=utf-8'
        ];
        $response = new Response(
            [
                'http_code' => 200,
                'headers'   => $headers
            ],
            '{"name":"Test"}'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($headers, $response->getHeaders());
        $this->assertEquals('W/"e79a1fdf3cf010530b6d6827549915ce"', $response->getHeader('ETag'));
        $this->assertEquals('Fri, 23 Mar 2018 14:57:17 GMT', $response->getHeader('Last-Modified'));
        $this->assertEquals('application/json; charset=utf-8', $response->getHeader('Content-Type'));
        $this->assertEquals('application/json; charset=utf-8', $response->getHeader('content-type'));
        $this->assertEquals('application/json; charset=utf-8', $response->getHeader('cOnTeNt-TyPe'));
        $this->assertEquals((object)['name' => 'Test'], $response->getBody());
    }

    public function testOther()
    {
        $headers = [
            'ETag'          => 'W/"e79a1fdf3cf010530b6d6827549915ce"',
            'Last-Modified' => 'Fri, 23 Mar 2018 14:57:17 GMT'
        ];
        $response = new Response(
            [
                'http_code' => 200,
                'headers'   => $headers
            ],
            'Test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($headers, $response->getHeaders());
        $this->assertNull($response->getHeader('Content-Type'));
        $this->assertEquals('Test', $response->getBody());
    }
}
