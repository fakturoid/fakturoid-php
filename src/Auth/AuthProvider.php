<?php

namespace Fakturoid\Auth;

use Fakturoid\Dispatcher;
use Fakturoid\Enum\AuthTypeEnum;
use Fakturoid\Exception\AuthorizationFailedException;
use Fakturoid\Exception\ClientErrorException;
use Fakturoid\Exception\ConnectionFailedException;
use Fakturoid\Exception\InvalidDataException;
use Fakturoid\Exception\RequestException;
use Fakturoid\Exception\ServerErrorException;
use JsonException;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class AuthProvider
{
    private ?string $code = null;
    private ?Credentials $credentials = null;
    private ?CredentialCallback $credentialsCallback = null;
    private string $clientId;
    private string $clientSecret;
    private ?string $redirectUri;
    private ClientInterface $client;

    public function __construct(
        string $clientId,
        string $clientSecret,
        ?string $redirectUri,
        ClientInterface $client
    ) {
        $this->client = $client;
        $this->redirectUri = $redirectUri;
        $this->clientSecret = $clientSecret;
        $this->clientId = $clientId;
    }

    public function auth(
        string $authType = AuthTypeEnum::AUTHORIZATION_CODE_FLOW,
        Credentials $credentials = null
    ): ?Credentials {
        $this->credentials = $credentials;
        switch ($authType) {
            case AuthTypeEnum::AUTHORIZATION_CODE_FLOW: return $this->authorizationCode();
            case AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW: return $this->clientCredentials();
            default: throw new \Exception();
        }
    }

    /**
     * @throws AuthorizationFailedException
     */
    private function authorizationCode(): ?Credentials
    {
        if ($this->credentials !== null) {
            return $this->credentials;
        }
        if (empty($this->code)) {
            throw new AuthorizationFailedException('Load authentication screen first.');
        }

        try {
            /** @var array{'access_token': string, 'expires_in': int, 'refresh_token': string, 'token_type': string, 'error'?:string} $json */
            $json = $this->makeRequest([
                'grant_type' => 'authorization_code',
                'code' => $this->code,
                'redirect_uri' => $this->redirectUri,
            ]);
        } catch (InvalidDataException | ConnectionFailedException $exception) {
            throw new AuthorizationFailedException(
                sprintf('An error occurred while authorization code flow. Message: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        } catch (RequestException $exception) {
            throw new AuthorizationFailedException(
                sprintf('Error occurred. Message: %s', $exception->getResponse()->getReasonPhrase()),
                $exception->getCode(),
                $exception
            );
        }
        $this->checkResponseWithAccessToken($json, AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
        /** @var array{'refresh_token': string, 'access_token': string, 'expires_in': int} $json */
        $this->credentials = new Credentials(
            $json['refresh_token'],
            $json['access_token'],
            (new \DateTimeImmutable())->modify('+ ' . ($json['expires_in'] - 10) . ' seconds'),
            AuthTypeEnum::AUTHORIZATION_CODE_FLOW
        );
        $this->callCredentialsCallback();
        return $this->credentials;
    }

    /**
     * @param array<string, mixed> $json
     * @return void
     * @throws AuthorizationFailedException
     */
    private function checkResponseWithAccessToken(array $json, string $authType): void
    {
        if (!empty($json['error'])) {
            throw new AuthorizationFailedException(
                sprintf('An error occurred while %s flow. Message: %s', $authType, $json['error'])
            );
        }
        if (empty($json['access_token']) || empty($json['expires_in'])) {
            throw new AuthorizationFailedException(
                sprintf('An error occurred while %s flow. Message: invalid response', $authType)
            );
        }
    }

    /**
     * @throws AuthorizationFailedException
     */
    public function oauth2Refresh(): ?Credentials
    {
        if ($this->credentials !== null) {
            $refreshToken = $this->credentials->getRefreshToken();
            try {
                $json = $this->makeRequest([
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken
                ]);
            } catch (InvalidDataException | ConnectionFailedException $exception) {
                throw new AuthorizationFailedException(
                    sprintf('Error occurred while refreshing token. Message: %s', $exception->getMessage()),
                    $exception->getCode(),
                    $exception
                );
            } catch (RequestException $exception) {
                throw new AuthorizationFailedException(
                    sprintf('Error occurred. Message: %s', $exception->getResponse()->getReasonPhrase()),
                    $exception->getCode(),
                    $exception
                );
            }

            $authType = AuthTypeEnum::AUTHORIZATION_CODE_FLOW;
            $this->checkResponseWithAccessToken($json, $authType);
            /** @var array{'access_token': string, 'token_type': string, 'expires_in': int} $json */
            $this->credentials = new Credentials(
                $refreshToken,
                $json['access_token'],
                (new \DateTimeImmutable())->modify('+ ' . ($json['expires_in'] - 10) . ' seconds'),
                $authType
            );
            $this->callCredentialsCallback();
            return $this->credentials;
        }
        return $this->credentials;
    }

    /**
     * @throws AuthorizationFailedException
     * @throws ClientErrorException
     * @throws ClientExceptionInterface
     * @throws ServerErrorException
     */
    public function revoke(): bool
    {
        if ($this->credentials === null) {
            throw new AuthorizationFailedException('Load authentication screen first.');
        }
        if ($this->credentials->getAuthType() !== AuthTypeEnum::AUTHORIZATION_CODE_FLOW) {
            throw new AuthorizationFailedException('Revoke is only available for authorization code flow');
        }
        try {
            $request = new Request(
                'POST',
                sprintf('%s/oauth/revoke', Dispatcher::BASE_URL),
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode(sprintf('%s:%s', $this->clientId, $this->clientSecret))
                ],
                json_encode(['token' => $this->credentials->getRefreshToken()], JSON_THROW_ON_ERROR)
            );
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $exception) {
            throw $exception;
        }
        $responseStatusCode = $response->getStatusCode();
        if ($responseStatusCode >= 400 && $responseStatusCode < 500) {
            throw new ClientErrorException($request, $response);
        }
        if ($responseStatusCode >= 500 && $responseStatusCode < 600) {
            throw new ServerErrorException($request, $response);
        }

        return $responseStatusCode === 200;
    }

    /**
     * @throws AuthorizationFailedException
     */
    public function reAuth(): ?Credentials
    {
        $credentials = $this->getCredentials();
        if (
            $credentials === null
            || empty($credentials->getAccessToken())
            || (empty($credentials->getRefreshToken()) && $credentials->getAuthType(
            ) === AuthTypeEnum::AUTHORIZATION_CODE_FLOW)
        ) {
            throw new AuthorizationFailedException('Invalid credentials');
        }
        if (!$credentials->isExpired()) {
            return $this->getCredentials();
        }
        switch ($credentials->getAuthType()) {
            case AuthTypeEnum::AUTHORIZATION_CODE_FLOW: return $this->oauth2Refresh();
            case AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW: return $this->auth(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW);
            default: throw new \Exception();
        }
    }

    /**
     * @throws AuthorizationFailedException
     */
    private function clientCredentials(): ?Credentials
    {
        try {
            $json = $this->makeRequest([
                'grant_type' => 'client_credentials',
            ]);
        } catch (InvalidDataException | ConnectionFailedException $exception) {
            throw new AuthorizationFailedException(
                sprintf('An error occurred while client credentials flow. Message: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        } catch (RequestException $exception) {
            throw new AuthorizationFailedException(
                sprintf('Error occurred. Message: %s', $exception->getResponse()->getReasonPhrase()),
                $exception->getCode(),
                $exception
            );
        }
        $this->checkResponseWithAccessToken($json, AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW);
        /** @var array{'refresh_token'?: string|null, 'access_token': string, 'expires_in': int} $json */
        $this->credentials = new Credentials(
            $json['refresh_token'] ?? null,
            $json['access_token'],
            (new \DateTimeImmutable())->modify('+ ' . ($json['expires_in'] - 10) . ' seconds'),
            AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW
        );
        $this->callCredentialsCallback();

        return $this->credentials;
    }

    /**
     * @param array<string, mixed> $body
     * @return array{'refresh_token'?: string|null, 'access_token': string, 'expires_in': int}|array{'error'?:string}
     * @throws ConnectionFailedException|InvalidDataException|RequestException
     */
    private function makeRequest(array $body): array
    {
        try {
            $request = new Request(
                'POST',
                sprintf('%s/oauth/token', Dispatcher::BASE_URL),
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode(sprintf('%s:%s', $this->clientId, $this->clientSecret))
                ],
                json_encode($body, JSON_THROW_ON_ERROR)
            );
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $exception) {
            throw new ConnectionFailedException(
                sprintf('Error occurred. Message: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        } catch (JsonException $exception) {
            throw new InvalidDataException(
                sprintf('Error occurred while decoding response. Message: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
        $responseStatusCode = $response->getStatusCode();
        if ($responseStatusCode >= 400 && $responseStatusCode < 500) {
            throw new ClientErrorException($request, $response);
        }
        if ($responseStatusCode >= 500 && $responseStatusCode < 600) {
            throw new ServerErrorException($request, $response);
        }
        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function getAuthenticationUrl(?string $state = null): string
    {
        return sprintf(
            '%s?client_id=%s&redirect_uri=%s&response_type=code',
            sprintf('%s/oauth', Dispatcher::BASE_URL),
            $this->clientId,
            $this->redirectUri
        ) . ($state !== null ? '&state=' . $state : null);
    }

    public function loadCode(string $code): void
    {
        $this->code = $code;
    }

    public function getCredentials(): ?Credentials
    {
        return $this->credentials;
    }

    private function callCredentialsCallback(): void
    {
        if ($this->credentialsCallback !== null) {
            call_user_func($this->credentialsCallback, $this->credentials);
        }
    }

    public function setCredentials(?Credentials $credentials): void
    {
        $this->credentials = $credentials;
    }

    public function setCredentialsCallback(CredentialCallback $callback): void
    {
        $this->credentialsCallback = $callback;
    }

    /**
     * @throws AuthorizationFailedException
     */
    public function requestCredentials(string $code): void
    {
        $this->loadCode($code);
        $this->auth(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
    }
}
