<?php

namespace Fakturoid\Tests\Unit;

use Fakturoid\Auth\AuthProvider;
use Fakturoid\Auth\Credentials;
use Fakturoid\Dispatcher;
use Fakturoid\Exception\ClientErrorException;
use Fakturoid\Exception\ServerErrorException;
use Psr\Http\Client\ClientInterface;

class RequestExceptionTest extends UnitTestCase
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

    public function test429WithRateLimitHeaders(): void
    {
        $responseInterface = $this->createPsrResponseMock(
            429,
            'application/json',
            '{"error":"Rate limit exceeded"}',
            [
                'X-RateLimit-Policy' => ['default;q=400;w=60'],
                'X-RateLimit' => ['default;r=0;t=45']
            ]
        );
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

        try {
            $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
            $this->fail('Expected ClientErrorException was not thrown');
        } catch (ClientErrorException $e) {
            $this->assertEquals(429, $e->getCode());
            $this->assertTrue($e->isRateLimitExceeded());

            // Test rate limit methods on Response object
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getRateLimitQuota());
            $this->assertEquals(60, $response->getRateLimitWindow());
            $this->assertEquals(0, $response->getRateLimitRemaining());
            $this->assertEquals(45, $response->getRateLimitReset());
        }
    }

    public function test400IsNotRateLimitExceeded(): void
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

        try {
            $dispatcher->patch('/accounts/{accountSlug}/invoices/1.json', ['name' => 'Test']);
            $this->fail('Expected ClientErrorException was not thrown');
        } catch (ClientErrorException $e) {
            $this->assertEquals(400, $e->getCode());
            $this->assertFalse($e->isRateLimitExceeded());
        }
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
