<?php

use PHPUnit\Framework\TestCase;

class FakturoidRequestTest extends TestCase
{
    public function testFullConstructor()
    {
        $request = new FakturoidRequest(array(
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
        $request = new FakturoidRequest(array(
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
        $request = new FakturoidRequest(array(
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
    }
}
