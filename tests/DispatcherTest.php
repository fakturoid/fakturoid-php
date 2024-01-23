<?php

namespace Fakturoid\Tests;

use Fakturoid\Auth\AuthProvider;
use Fakturoid\Auth\Credentials;
use Fakturoid\Dispatcher;
use Fakturoid\Exception\ClientErrorException;
use Fakturoid\Exception\Exception;
use Fakturoid\Exception\ServerErrorException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class DispatcherTest extends TestCase
{
    public function testRequiredAccountSlugMissing(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $authProvider = $this->createMock(AuthProvider::class);

        $dispatcher = new Dispatcher('test', $authProvider, $client);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Account slug is not set. You must set it before calling this method.');
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function testRequiredAccountSlug(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher('test', $authProvider, $client, 'test');
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function testNotRequiredAccountSlugMissing(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher('test', $authProvider, $client);
        $dispatcher->patch('/accounts/invoices/1.json', ['name' => 'Test']);
    }

    public function testNotRequiredAccountSlug(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher('test', $authProvider, $client, 'test');
        $dispatcher->patch('/accounts/invoices/1.json', ['name' => 'Test']);
    }
}
