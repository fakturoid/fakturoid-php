<?php

namespace Fakturoid\Auth;

use Fakturoid\Dispatcher;
use Fakturoid\Enum\AuthTypeEnum;
use Fakturoid\Exception\AuthorizationFailedException;
use Fakturoid\Exception\ClientErrorException;
use Fakturoid\Exception\ConnectionFailedException;
use Fakturoid\Exception\InvalidDataException;
use Fakturoid\Exception\InvalidResponseException;
use Fakturoid\Exception\RequestException;
use Fakturoid\Exception\ServerErrorException;
use Fakturoid\Response;
use JsonException;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

/**
 * @phpstan-type ClientCodeRequest array{
 *     grant_type: 'client_credentials'
 * }
 * @phpstan-type AuthorizationFlowRefresh array{
 *     grant_type: 'refresh_token',
 *     refresh_token: string|null
 * }
 * @phpstan-type AuthorizationFlowRequest array{
 *     grant_type: 'authorization_code',
 *     code: string|null,
 *     redirect_uri: string|null
 * }
 * @phpstan-type AuthTokenErrorResponse array{error: string}
 */
class AuthProvider
{
    private ?string $code = null;
    private ?Credentials $credentials = null;
    private ?CredentialCallback $credentialsCallback = null;

    public function __construct(
        #[\SensitiveParameter] private readonly string $clientId,
        #[\SensitiveParameter] private readonly string $clientSecret,
        private readonly ?string $redirectUri,
        private readonly ClientInterface $client
    ) {
    }

    /**
     * @throws AuthorizationFailedException
     */
    public function auth(
        AuthTypeEnum $authType = AuthTypeEnum::AUTHORIZATION_CODE_FLOW,
        ?Credentials $credentials = null
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
            $accessToken = $this->makeRequest(
                [
                    'grant_type' => 'authorization_code',
                    'code' => $this->code,
                    'redirect_uri' => $this->redirectUri,
                ]
            );
        } catch (InvalidDataException | ConnectionFailedException $exception) {
            throw new AuthorizationFailedException(
                sprintf('An error occurred while authorization code flow. Message: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        } catch (RequestException $exception) {
            throw new AuthorizationFailedException(
                sprintf(
                    'Error occurred. Message: %s',
                    $exception->getResponse()->getOriginalResponse()->getReasonPhrase()
                ),
                $exception->getCode(),
                $exception
            );
        }
        $this->credentials = new Credentials(
            $accessToken->refreshToken,
            $accessToken->accessToken,
            (new \DateTimeImmutable())->modify('+ ' . ($accessToken->expiresIn - 10) . ' seconds'),
            AuthTypeEnum::AUTHORIZATION_CODE_FLOW
        );
        $this->callCredentialsCallback();
        return $this->credentials;
    }

    /**
     * @param array<string, mixed> $json
     * @phpstan-assert-if-true AuthTokenErrorResponse $json
     */
    private function assertResponseIsError(array $json): bool
    {
        return is_string($json['error'] ?? null);
    }

    /**
     * @throws AuthorizationFailedException
     */
    public function oauth2Refresh(): ?Credentials
    {
        if ($this->credentials !== null) {
            $refreshToken = $this->credentials->getRefreshToken();
            try {
                $accessToken = $this->makeRequest(
                    [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $refreshToken,
                    ]
                );
            } catch (InvalidDataException | ConnectionFailedException $exception) {
                throw new AuthorizationFailedException(
                    sprintf('Error occurred while refreshing token. Message: %s', $exception->getMessage()),
                    $exception->getCode(),
                    $exception
                );
            } catch (RequestException $exception) {
                throw new AuthorizationFailedException(
                    sprintf(
                        'Error occurred. Message: %s',
                        $exception->getResponse()->getOriginalResponse()->getReasonPhrase()
                    ),
                    $exception->getCode(),
                    $exception
                );
            }

            $this->credentials = new Credentials(
                $accessToken->refreshToken ?? $refreshToken,
                $accessToken->accessToken,
                (new \DateTimeImmutable())->modify('+ ' . ($accessToken->expiresIn - 10) . ' seconds'),
                $this->credentials->getAuthType()
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
        if ($this->credentials->getAuthType()->value !== AuthTypeEnum::AUTHORIZATION_CODE_FLOW->value) {
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
        } catch (JsonException $exception) {
            throw new AuthorizationFailedException('Failed to encode data to JSON', $exception->getCode(), $exception);
        }
        $wrappedResponse = new Response($response);
        $responseStatusCode = $wrappedResponse->getStatusCode();
        if ($responseStatusCode >= 400 && $responseStatusCode < 500) {
            throw new ClientErrorException($request, $wrappedResponse);
        }
        if ($responseStatusCode >= 500 && $responseStatusCode < 600) {
            throw new ServerErrorException($request, $wrappedResponse);
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
            $credentials === null ||
            empty($credentials->getAccessToken()) ||
            (
                empty($credentials->getRefreshToken()) &&
                $credentials->getAuthType() === AuthTypeEnum::AUTHORIZATION_CODE_FLOW
            )
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
            $accessToken = $this->makeRequest(['grant_type' => 'client_credentials']);
        } catch (InvalidDataException | ConnectionFailedException $exception) {
            throw new AuthorizationFailedException(
                sprintf('An error occurred while client credentials flow. Message: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        } catch (RequestException $exception) {
            throw new AuthorizationFailedException(
                sprintf(
                    'Error occurred. Message: %s',
                    $exception->getResponse()->getOriginalResponse()->getReasonPhrase()
                ),
                $exception->getCode(),
                $exception
            );
        }
        $this->credentials = new Credentials(
            $accessToken->refreshToken,
            $accessToken->accessToken,
            (new \DateTimeImmutable())->modify('+ ' . ($accessToken->expiresIn - 10) . ' seconds'),
            AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW
        );
        $this->callCredentialsCallback();

        return $this->credentials;
    }

    /**
     * @param ClientCodeRequest|AuthorizationFlowRefresh|AuthorizationFlowRequest $body
     * @throws ConnectionFailedException|InvalidDataException|RequestException
     */
    private function makeRequest(array $body): AccessToken
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
        $wrappedResponse = new Response($response);
        $responseStatusCode = $wrappedResponse->getStatusCode();
        if ($responseStatusCode >= 400 && $responseStatusCode < 500) {
            throw new ClientErrorException($request, $wrappedResponse);
        }
        if ($responseStatusCode >= 500 && $responseStatusCode < 600) {
            throw new ServerErrorException($request, $wrappedResponse);
        }
        try {
            /** @var array<string, mixed> $responseData */
            $responseData = $wrappedResponse->getBody(true);
        } catch (InvalidResponseException $exception) {
            throw new InvalidDataException(
                sprintf('Error occurred while decoding response. Message: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
        if ($this->assertResponseIsError($responseData)) {
            throw new InvalidDataException(sprintf('Error: %s', $responseData['error']));
        }
        $authType = match ($body['grant_type']) {
            'client_credentials', 'refresh_token' => AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW,
            'authorization_code' => AuthTypeEnum::AUTHORIZATION_CODE_FLOW,
        };
        try {
            return AccessToken::create($responseData, $authType);
        } catch (\InvalidArgumentException $argumentException) {
            throw new AuthorizationFailedException(
                sprintf('Error occurred while processing response. Message: %s', $argumentException->getMessage()),
                $argumentException->getCode(),
                $argumentException
            );
        }
    }

    public function getAuthenticationUrl(?string $state = null): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
        ];

        if ($state !== null) {
            $params['state'] = $state;
        }

        return sprintf('%s/oauth?%s', Dispatcher::BASE_URL, http_build_query($params));
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
