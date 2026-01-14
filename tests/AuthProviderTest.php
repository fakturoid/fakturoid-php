<?php

namespace Fakturoid\Tests;

use Fakturoid\Auth\AuthProvider;
use Fakturoid\Auth\CredentialCallback;
use Fakturoid\Auth\Credentials;
use Fakturoid\Enum\AuthTypeEnum;
use Fakturoid\Exception\AuthorizationFailedException;
use Fakturoid\Exception\ClientErrorException;
use Fakturoid\Exception\ServerErrorException;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class AuthProviderTest extends TestCase
{
    public function testAuthenticationUrl(): void
    {
        $requester = $this->createMock(ClientInterface::class);
        $authProvider = new AuthProvider('clientId', 'clientSecret', 'redirectUri', $requester);

        $baseUrl = 'https://app.fakturoid.cz/api/v3/oauth';
        $expectedUrl = $baseUrl . '?client_id=clientId&redirect_uri=redirectUri&response_type=code&state=c';
        $this->assertEquals(
            $expectedUrl,
            $authProvider->getAuthenticationUrl('c')
        );
    }

    public function testAuthenticationUrlWithoutState(): void
    {
        $requester = $this->createMock(ClientInterface::class);
        $authProvider = new AuthProvider('clientId', 'clientSecret', 'redirectUri', $requester);

        $this->assertEquals(
            'https://app.fakturoid.cz/api/v3/oauth?client_id=clientId&redirect_uri=redirectUri&response_type=code',
            $authProvider->getAuthenticationUrl()
        );
    }

    public function testAuthorizationCodeReAuthWithEmptyRefreshCode(): void
    {
        $requester = $this->createMock(ClientInterface::class);

        $credentials = $this->createMock(Credentials::class);
        $credentials->method('getAuthType')
            ->willReturn(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
        $credentials->method('getAccessToken')
            ->willReturn('access_token');
        $credentials->method('getRefreshToken')
            ->willReturn(null);

        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $requester);
        $authProvider->setCredentials($credentials);
        $this->expectException(AuthorizationFailedException::class);
        $this->expectExceptionMessage('Invalid credentials');
        $authProvider->reAuth();
    }

    public function testEmptyCredentialsReAuth(): void
    {
        $requester = $this->createMock(ClientInterface::class);

        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $requester);
        $this->expectException(AuthorizationFailedException::class);
        $this->expectExceptionMessage('Invalid credentials');
        $authProvider->reAuth();
    }

    public function testAuthorizationCodeReAuth(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock('{"refresh_token":"","access_token":"access_token","expires_in":7200}'));

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $credentials = $this->createMock(Credentials::class);
        $credentials->method('getAuthType')
            ->willReturn(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
        $credentials
            ->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('access_token');
        $credentials
            ->expects($this->exactly(2))
            ->method('getRefreshToken')
            ->willReturn('refresh_token');
        $credentials
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(true);

        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);

        $credentialCallback = $this->createMock(CredentialCallback::class);
        $credentialCallback->expects($this->once())
            ->method('__invoke');
        $authProvider->setCredentialsCallback($credentialCallback);
        $authProvider->setCredentials($credentials);
        $authProvider->reAuth();
    }

    public function testClientCredentialReAuth(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock('{"refresh_token":"","access_token":"access_token","expires_in":7200}'));

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $credentials = $this->createMock(Credentials::class);
        $credentials->method('getAuthType')
            ->willReturn(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
        $credentials
            ->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('access_token');
        $credentials
            ->expects($this->exactly(2))
            ->method('getRefreshToken')
            ->willReturn('refresh_token');
        $credentials
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(true);

        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);

        $credentialCallback = $this->createMock(CredentialCallback::class);
        $credentialCallback->expects($this->once())
            ->method('__invoke');
        $authProvider->setCredentialsCallback($credentialCallback);
        $authProvider->setCredentials($credentials);
        $authProvider->reAuth();
    }

    public function testClientCredentialReAuthWithError(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock('{"error":"invalid_grant"}'));

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $credentials = $this->createMock(Credentials::class);
        $credentials->method('getAuthType')
            ->willReturn(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
        $credentials
            ->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('access_token');
        $credentials
            ->expects($this->exactly(2))
            ->method('getRefreshToken')
            ->willReturn('refresh_token');
        $credentials
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(true);

        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);
        $this->expectException(AuthorizationFailedException::class);
        $this->expectExceptionMessage('An error occurred while authorization_code flow. Message:');
        $authProvider->setCredentials($credentials);
        $authProvider->reAuth();
    }

    public function testClientCredentialReAuthWithoutResponse(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock('{}'));

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $credentials = $this->createMock(Credentials::class);
        $credentials->method('getAuthType')
            ->willReturn(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
        $credentials
            ->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('access_token');
        $credentials
            ->expects($this->exactly(2))
            ->method('getRefreshToken')
            ->willReturn('refresh_token');
        $credentials
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(true);

        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);
        $this->expectException(AuthorizationFailedException::class);
        $this->expectExceptionMessage('An error occurred while authorization_code flow. Message:');
        $authProvider->setCredentials($credentials);
        $authProvider->reAuth();
    }

    public function testClientCredential(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock('{"refresh_token":"","access_token":"access_token","expires_in":7200}'));

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);
        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);

        $credentialCallback = $this->createMock(CredentialCallback::class);
        $credentialCallback->expects($this->once())
            ->method('__invoke');
        $authProvider->setCredentialsCallback($credentialCallback);
        $credentials = $authProvider->auth(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW);
        $this->assertInstanceOf(Credentials::class, $credentials);
        $this->assertEquals('access_token', $credentials->getAccessToken());
        $this->assertEquals(null, $credentials->getRefreshToken());
        $this->assertEquals(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW, $credentials->getAuthType());
    }

    public function testClientCredentialWithEmptyResponse(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->getStreamMock('{}'));

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);
        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);

        $this->expectException(AuthorizationFailedException::class);
        $this->expectExceptionMessage('An error occurred while client_credentials flow. Message: invalid response');
        $authProvider->auth(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW);
    }

    public function testAuthorizationCodeWithoutCode(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);

        $this->expectException(AuthorizationFailedException::class);
        $this->expectExceptionMessage('Load authentication screen first.');
        $authProvider->auth(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
    }

    public function testRevoke(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->method('getBody')
            ->willReturn($this->getStreamMock('{}'));
        $responseInterface
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $credentials = $this->createMock(Credentials::class);
        $credentials->method('getAuthType')
            ->willReturn(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
        $credentials
            ->expects($this->once())
            ->method('getRefreshToken')
            ->willReturn('refresh_token');

        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);

        $authProvider->setCredentials($credentials);
        $this->assertTrue($authProvider->revoke());
    }

    public function testRevoke500(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->method('getBody')
            ->willReturn($this->getStreamMock('{}'));
        $responseInterface
            ->method('getStatusCode')
            ->willReturn(500);

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $credentials = $this->createMock(Credentials::class);
        $credentials->method('getAuthType')
            ->willReturn(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
        $credentials
            ->expects($this->once())
            ->method('getRefreshToken')
            ->willReturn('refresh_token');

        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);

        $authProvider->setCredentials($credentials);
        $this->expectException(ServerErrorException::class);
        $authProvider->revoke();
    }

    public function testRevoke400(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->method('getBody')
            ->willReturn($this->getStreamMock('{}'));
        $responseInterface
            ->method('getStatusCode')
            ->willReturn(400);

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);

        $credentials = $this->createMock(Credentials::class);
        $credentials->method('getAuthType')
            ->willReturn(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
        $credentials
            ->expects($this->once())
            ->method('getRefreshToken')
            ->willReturn('refresh_token');

        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);

        $authProvider->setCredentials($credentials);
        $this->expectException(ClientErrorException::class);
        $authProvider->revoke();
    }

    public function testRevokeClientCredentials(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $credentials = $this->createMock(Credentials::class);
        $credentials->method('getAuthType')
            ->willReturn(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW);
        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);

        $authProvider->setCredentials($credentials);
        $this->expectException(AuthorizationFailedException::class);
        $this->expectExceptionMessage('Revoke is only available for authorization code flow');
        $authProvider->revoke();
    }

    public function testRevokeWithoutCredentials(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $authProvider = new AuthProvider('clientId', 'clientSecret', null, $client);

        $this->expectException(AuthorizationFailedException::class);
        $this->expectExceptionMessage('Load authentication screen first.');
        $authProvider->revoke();
    }

    public function testAuthorizationCodeSimple(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(
                $this->getStreamMock(
                    '{"refresh_token":"refresh_token","access_token":"access_token","expires_in":7200}'
                )
            );

        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);
        $authProvider = new AuthProvider('clientId', 'clientSecret', 'redirectUri', $client);

        $credentialCallback = $this->createMock(CredentialCallback::class);
        $credentialCallback->expects($this->once())
            ->method('__invoke');
        $authProvider->setCredentialsCallback($credentialCallback);
        $authProvider->requestCredentials('CODE');
        $credentials = $authProvider->getCredentials();
        $this->assertInstanceOf(Credentials::class, $credentials);
        $this->assertEquals('access_token', $credentials->getAccessToken());
        $this->assertEquals('refresh_token', $credentials->getRefreshToken());
        $this->assertEquals(AuthTypeEnum::AUTHORIZATION_CODE_FLOW, $credentials->getAuthType());
    }

    public function testAuthorizationInvalidResponse(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(
                $this->getStreamMock('{}')
            );

        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);
        $authProvider = new AuthProvider('clientId', 'clientSecret', 'redirectUri', $client);
        $this->expectException(AuthorizationFailedException::class);
        $this->expectExceptionMessage('An error occurred while authorization_code flow. Message:');
        $authProvider->requestCredentials('CODE');
    }

    public function testAuthorizationInvalidResponse2(): void
    {
        $exception = new class ('test') extends \Exception implements ClientExceptionInterface {
        };
        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects($this->once())
            ->method('sendRequest')
            ->willThrowException($exception);
        $authProvider = new AuthProvider('clientId', 'clientSecret', 'redirectUri', $client);
        $this->expectException(AuthorizationFailedException::class);
        $this->expectExceptionMessage('An error occurred while authorization code flow. Message: ');
        $authProvider->requestCredentials('CODE');
    }

    public function testAuthorizationCode(): void
    {
        $responseInterface = $this->getResponseJsonMock();
        $responseInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(
                $this->getStreamMock(
                    '{"refresh_token":"refresh_token","access_token":"access_token","expires_in":7200}'
                )
            );

        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseInterface);
        $authProvider = new AuthProvider('clientId', 'clientSecret', 'redirectUri', $client);
        $authProvider->loadCode('CODE');

        $credentialCallback = $this->createMock(CredentialCallback::class);
        $credentialCallback->expects($this->once())
            ->method('__invoke');
        $authProvider->setCredentialsCallback($credentialCallback);

        $credentials = $authProvider->auth(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
        $this->assertInstanceOf(Credentials::class, $credentials);
        $this->assertEquals('access_token', $credentials->getAccessToken());
        $this->assertEquals('refresh_token', $credentials->getRefreshToken());
        $this->assertEquals(AuthTypeEnum::AUTHORIZATION_CODE_FLOW, $credentials->getAuthType());
    }

    private function getResponseJsonMock(): MockObject& ResponseInterface
    {
        $responseInterface = $this->createMock(ResponseInterface::class);
        $responseInterface->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['Content-Type' => ['application/json']]);
        $responseInterface
            ->expects($this->once())
            ->method('getHeaderLine')
            ->with('Content-Type')
            ->willReturn('application/json');

        return $responseInterface;
    }
}
