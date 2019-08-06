<?php

use PHPUnit\Framework\TestCase;
use Fakturoid\Request as Request;

class RequestTest extends TestCase
{
    public function testFullConstructor()
    {
        $request = new Request(array(
            'url'     => 'https://app.fakturoid.cz/api/v2/accounts/invoices.json',
            'method'  => 'get',
            'params'  => array('page' => 2),
            'body'    => json_encode(array('name' => 'Test')),
            'userpwd' => 'test:123456',
            'headers' => array(
                'User-Agent' => 'Test <test@example.org>'
            )
        ));

        $this->assertEquals('https://app.fakturoid.cz/api/v2/accounts/invoices.json?page=2', $request->getUrl());
        $this->assertEquals('get', $request->getMethod());
        $this->assertEquals('{"name":"Test"}', $request->getBody());
        $this->assertEquals('test:123456', $request->getUserpwd());
        $this->assertEquals('Test <test@example.org>', $request->getHeader('User-Agent'));
    }

    public function testMediumConstructor()
    {
        $request = new Request(array(
            'url'     => 'https://app.fakturoid.cz/api/v2/accounts/invoices.json',
            'method'  => 'get',
            'params'  => array('page' => null),
            'userpwd' => 'test:123456',
            'headers' => array(
                'User-Agent' => 'Test <test@example.org>'
            )
        ));

        $this->assertEquals('https://app.fakturoid.cz/api/v2/accounts/invoices.json', $request->getUrl());
        $this->assertEquals('get', $request->getMethod());
        $this->assertEquals(null, $request->getBody());
        $this->assertEquals('test:123456', $request->getUserpwd());
        $this->assertEquals('Test <test@example.org>', $request->getHeader('User-Agent'));
    }

    public function testMinimalConstructor()
    {
        $request = new Request(array(
            'url'     => 'https://app.fakturoid.cz/api/v2/accounts/invoices.json',
            'method'  => 'get',
            'userpwd' => 'test:123456',
            'headers' => array(
                'User-Agent' => 'Test <test@example.org>'
            )
        ));

        $this->assertEquals('https://app.fakturoid.cz/api/v2/accounts/invoices.json', $request->getUrl());
        $this->assertEquals('get', $request->getMethod());
        $this->assertEquals(null, $request->getBody());
        $this->assertEquals('test:123456', $request->getUserpwd());
        $this->assertEquals('Test <test@example.org>', $request->getHeader('User-Agent'));
        $this->assertEquals('Test <test@example.org>', $request->getHeader('user-agent'));
        $this->assertEquals('Test <test@example.org>', $request->getHeader('uSeR-aGeNt'));
    }

    public function testGetHttpHeaders()
    {
        $request = new Request(array(
            'url'     => 'https://app.fakturoid.cz/api/v2/accounts/invoices.json',
            'method'  => 'get',
            'userpwd' => 'test:123456',
            'headers' => array(
                'User-Agent' => 'Test <test@example.org>'
            )
        ));
        $headers   = $request->getHttpHeaders();
        $clientEnv = $headers[0];

        $this->assertRegExp('/PHP \d+\.\d+\.\d+/', $clientEnv);
    }
}
