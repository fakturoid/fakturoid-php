<?php

use PHPUnit\Framework\TestCase;

class FakturoidResponseTest extends TestCase
{
    public function testJson()
    {
        $headers = array(
            'ETag'          => 'W/"e79a1fdf3cf010530b6d6827549915ce"',
            'Last-Modified' => 'Fri, 23 Mar 2018 14:57:17 GMT',
            'Content-Type'  => 'application/json; charset=utf-8'
        );
        $response = new FakturoidResponse(
            array(
                'http_code' => 200,
                'headers'   => $headers
            ),
            '{"name":"Test"}'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($headers, $response->getHeaders());
        $this->assertEquals('Fri, 23 Mar 2018 14:57:17 GMT', $response->getHeader('Last-Modified'));
        $this->assertEquals((object) array('name' => 'Test'), $response->getBody());
    }

    public function testOther()
    {
        $headers = array(
            'ETag'          => 'W/"e79a1fdf3cf010530b6d6827549915ce"',
            'Last-Modified' => 'Fri, 23 Mar 2018 14:57:17 GMT'
        );
        $response = new FakturoidResponse(
            array(
                'http_code' => 200,
                'headers'   => $headers
            ),
            'Test'
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($headers, $response->getHeaders());
        $this->assertNull($response->getHeader('Content-Type'));
        $this->assertEquals('Test', $response->getBody());
    }
}
