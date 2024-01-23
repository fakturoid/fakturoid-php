<?php

namespace Fakturoid\Auth;

use Fakturoid\Dispatcher;
use Fakturoid\Enum\AuthTypeEnum;
use Fakturoid\Exception\AuthorizationFailedException;
use Fakturoid\Exception\ConnectionFailedException;
use Fakturoid\Exception\InvalidDataException;
use JsonException;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class AuthProvider
{
    private ?string $code = null;
    private ?Credentials $credentials = null;
    private ?CredentialCallback $credentialsCallback = null;

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly ?string $redirectUri,
        private readonly ClientInterface $client
    ) {
    }

    public function auth(
        AuthTypeEnum $authType = AuthTypeEnum::AUTHORIZATION_CODE_FLOW,
        Credentials $credentials = null
    ): ?Credentials {
        $this->credentials = $credentials;
        return match ($authType) {
            AuthTypeEnum::AUTHORIZATION_CODE_FLOW => $this->authorizationCode(),
            AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW => $this->clientCredentials()
        };
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
        }
        if (!empty($json['error'])) {
            throw new AuthorizationFailedException(
                sprintf('An error occurred while authorization code flow. Message: %s', $json['error'])
            );
        }
        if (empty($json['access_token']) || empty($json['expires_in'])) {
            throw new AuthorizationFailedException(
                'An error occurred while authorization code flow. Message: invalid response'
            );
        }
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
     * @throws AuthorizationFailedException
     */
    public function oauth2Refresh(): ?Credentials
    {
        if ($this->credentials !== null) {
            try {
                $json = $this->makeRequest([
                    'grant_type' => 'refresh_token',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $this->credentials->getRefreshToken(),
                    'redirect_uri' => $this->redirectUri,
                ]);
            } catch (InvalidDataException | ConnectionFailedException $exception) {
                throw new AuthorizationFailedException(
                    sprintf('Error occurred while refreshing token. Message: %s', $exception->getMessage()),
                    $exception->getCode(),
                    $exception
                );
            }
            if (!empty($json['error'])) {
                throw new AuthorizationFailedException(
                    sprintf('Error occurred while refreshing token. Message: %s', $json['error'])
                );
            }
            if (empty($json['access_token']) || empty($json['expires_in'])) {
                throw new AuthorizationFailedException(
                    'Error occurred while refreshing token. Message: invalid response'
                );
            }
            $this->credentials = new Credentials(
                $json['refresh_token'] ?? null,
                $json['access_token'],
                (new \DateTimeImmutable())->modify('+ ' . ($json['expires_in'] - 10) . ' seconds'),
                $this->credentials->getAuthType()
            );
            $this->credentials->setAuthType(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
            $this->callCredentialsCallback();
            return $this->credentials;
        }
        return $this->credentials;
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
        return match ($credentials->getAuthType()) {
            AuthTypeEnum::AUTHORIZATION_CODE_FLOW => $this->oauth2Refresh(),
            AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW => $this->auth(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW)
        };
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
        }
        if (!empty($json['error'])) {
            throw new AuthorizationFailedException(
                sprintf('An error occurred while client credentials flow. Message: %s', $json['error'])
            );
        }
        if (empty($json['access_token']) || empty($json['expires_in'])) {
            throw new AuthorizationFailedException(
                'An error occurred while client credentials flow. Message: invalid response'
            );
        }
        $this->credentials = new Credentials(
            $json['refresh_token'] ?? null,
            $json['access_token'],
            (new \DateTimeImmutable())->modify('+ ' . ($json['expires_in'] - 10) . ' seconds'),
            AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW
        );
        $this->credentials->setAuthType(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW);
        $this->callCredentialsCallback();

        return $this->credentials;
    }

    /**
     * @param array<string, mixed> $body
     * @return array{'refresh_token'?: string|null, 'access_token': string, 'expires_in': int}|array{'error'?:string}
     * @throws ConnectionFailedException|InvalidDataException
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
            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
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
