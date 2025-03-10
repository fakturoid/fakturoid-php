<?php

namespace Fakturoid\Tests;

use Fakturoid\Auth\AuthProvider;
use Fakturoid\Auth\Credentials;
use Fakturoid\Dispatcher;
use Fakturoid\Exception\ClientErrorException;
use Fakturoid\Exception\ServerErrorException;
use Psr\Http\Client\ClientInterface;

class RequestExceptionTest extends TestCase
{
    public function test404(): void
    {
        $responseInterface = $this->createPsrResponseMock(404, 'application/json', '{"error":""}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher($authProvider, $client);
        $dispatcher->setAccountSlug('account-slug');
        $this->expectException(ClientErrorException::class);
        $this->expectExceptionCode(404);
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function test400(): void
    {
        $responseInterface = $this->createPsrResponseMock(400, 'application/json', '{"error":""}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher($authProvider, $client);
        $dispatcher->setAccountSlug('account-slug');
        $this->expectException(ClientErrorException::class);
        $this->expectExceptionCode(400);
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function test401(): void
    {
        $responseInterface = $this->createPsrResponseMock(401, 'application/json', '{"error":""}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher($authProvider, $client);
        $dispatcher->setAccountSlug('account-slug');
        $this->expectException(ClientErrorException::class);
        $this->expectExceptionCode(401);
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function test402(): void
    {
        $responseInterface = $this->createPsrResponseMock(402, 'application/json', '{"error":""}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher($authProvider, $client);
        $dispatcher->setAccountSlug('account-slug');
        $this->expectException(ClientErrorException::class);
        $this->expectExceptionCode(402);
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function test403(): void
    {
        $responseInterface = $this->createPsrResponseMock(403, 'application/json', '{"error":""}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher($authProvider, $client);
        $dispatcher->setAccountSlug('account-slug');
        $this->expectException(ClientErrorException::class);
        $this->expectExceptionCode(403);
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function test415(): void
    {
        $responseInterface = $this->createPsrResponseMock(415, 'application/json', '{"error":""}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher($authProvider, $client);
        $dispatcher->setAccountSlug('account-slug');
        $this->expectException(ClientErrorException::class);
        $this->expectExceptionCode(415);
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function test422(): void
    {
        $responseInterface = $this->createPsrResponseMock(422, 'application/json', '{"error":""}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher($authProvider, $client);
        $dispatcher->setAccountSlug('account-slug');
        $this->expectException(ClientErrorException::class);
        $this->expectExceptionCode(422);
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function test429(): void
    {
        $responseInterface = $this->createPsrResponseMock(429, 'application/json', '{"error":""}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher($authProvider, $client);
        $dispatcher->setAccountSlug('account-slug');
        $this->expectException(ClientErrorException::class);
        $this->expectExceptionCode(429);
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function testOtherClient(): void
    {
        $responseInterface = $this->createPsrResponseMock(499, 'application/json', '{"error":""}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher($authProvider, $client);
        $dispatcher->setAccountSlug('account-slug');
        $this->expectException(ClientErrorException::class);
        $this->expectExceptionCode(499);
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function testOtherServer(): void
    {
        $responseInterface = $this->createPsrResponseMock(599, 'application/json', '{"error":""}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher($authProvider, $client);
        $dispatcher->setAccountSlug('account-slug');
        $this->expectException(ServerErrorException::class);
        $this->expectExceptionCode(599);
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }

    public function test503(): void
    {
        $responseInterface = $this->createPsrResponseMock(503, 'application/json', '{"error":""}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $authProvider = $this->createMock(AuthProvider::class);
        $credentials = $this->createMock(Credentials::class);
        $credentials->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('test');
        $authProvider->expects($this->exactly(2))
            ->method('getCredentials')
            ->willReturn($credentials);

        $dispatcher = new Dispatcher($authProvider, $client);
        $dispatcher->setAccountSlug('account-slug');
        $this->expectException(ServerErrorException::class);
        $this->expectExceptionCode(503);
        $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
    }
}
