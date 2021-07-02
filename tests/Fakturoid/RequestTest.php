<?php

declare(strict_types=1);

namespace fakturoid\fakturoid_php\Test;


use fakturoid\fakturoid_php\Request as Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    protected string $url = 'https://app.fakturoid.cz/api/v2/accounts/invoices.json';

    public function testFullConstructor()
    {
        $request = new Request(
            [
                'url'     => $this->url,
                'method'  => 'get',
                'params'  => ['page' => 2],
                'body'    => json_encode(['name' => 'Test']),
                'userpwd' => 'test:123456',
                'headers' => [
                    'User-Agent' => 'Test <test@example.org>'
                ]
            ]
        );

        $this->assertEquals($this->url . '?page=2', $request->getUrl());
        $this->assertEquals('get', $request->getMethod());
        $this->assertEquals('{"name":"Test"}', $request->getBody());
        $this->assertEquals('test:123456', $request->getUserpwd());
        $this->assertEquals('Test <test@example.org>', $request->getHeader('User-Agent'));
    }

    public function testMediumConstructor()
    {
        $request = new Request(
            [
                'url'     => $this->url,
                'method'  => 'get',
                'params'  => ['page' => null],
                'userpwd' => 'test:123456',
                'headers' => ['User-Agent' => 'Test <test@example.org>']
            ]
        );

        $this->assertEquals($this->url, $request->getUrl());
        $this->assertEquals('get', $request->getMethod());
        $this->assertEquals(null, $request->getBody());
        $this->assertEquals('test:123456', $request->getUserpwd());
        $this->assertEquals('Test <test@example.org>', $request->getHeader('User-Agent'));
    }

    public function testMinimalConstructor()
    {
        $request = new Request(
            [
                'url'     => $this->url,
                'method'  => 'get',
                'userpwd' => 'test:123456',
                'headers' => [
                    'User-Agent' => 'Test <test@example.org>'
                ]
            ]
        );

        $this->assertEquals($this->url, $request->getUrl());
        $this->assertEquals('get', $request->getMethod());
        $this->assertEquals(null, $request->getBody());
        $this->assertEquals('test:123456', $request->getUserpwd());
        $this->assertEquals('Test <test@example.org>', $request->getHeader('User-Agent'));
        $this->assertEquals('Test <test@example.org>', $request->getHeader('user-agent'));
        $this->assertEquals('Test <test@example.org>', $request->getHeader('uSeR-aGeNt'));
    }

    public function testGetHttpHeaders()
    {
        $request = new Request(
            [
                'url'     => $this->url,
                'method'  => 'get',
                'userpwd' => 'test:123456',
                'headers' => [
                    'User-Agent' => 'Test <test@example.org>'
                ]
            ]
        );
        $headers = $request->getHttpHeaders();
        $clientEnv = $headers[ 0 ];

        $this->assertMatchesRegularExpression('/PHP \d+\.\d+\.\d+/', $clientEnv);
    }
}
