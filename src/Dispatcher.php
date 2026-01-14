<?php

namespace Fakturoid;

use Fakturoid\Auth\AuthProvider;
use Fakturoid\Exception\AuthorizationFailedException;
use Fakturoid\Exception\ClientErrorException;
use Fakturoid\Exception\ConnectionFailedException;
use Fakturoid\Exception\Exception;
use Fakturoid\Exception\InvalidDataException;
use Fakturoid\Exception\RequestException;
use Fakturoid\Exception\ServerErrorException;
use JsonException;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class Dispatcher implements DispatcherInterface
{
    final public const BASE_URL = 'https://app.fakturoid.cz/api/v3';

    public function __construct(
        private readonly AuthProvider $authorization,
        private readonly ClientInterface $client,
        private ?string $accountSlug = null
    ) {
    }

    public function setAccountSlug(string $accountSlug): void
    {
        $this->accountSlug = $accountSlug;
    }

    public function get(string $path, array $queryParams = []): Response
    {
        return $this->dispatch($path, ['method' => 'GET', 'params' => $queryParams]);
    }

    public function post(string $path, array $data = []): Response
    {
        return $this->dispatch($path, ['method' => 'POST', 'data' => $data]);
    }

    public function patch(string $path, array $data): Response
    {
        return $this->dispatch($path, ['method' => 'PATCH', 'data' => $data]);
    }

    public function delete(string $path): Response
    {
        return $this->dispatch($path, ['method' => 'DELETE']);
    }

    /**
     * @param array{'method': string, 'params'?: array<string, mixed>, 'data'?: array<string, mixed>} $options
     * @throws ConnectionFailedException|InvalidDataException|AuthorizationFailedException|RequestException|Exception
     */
    private function dispatch(string $path, array $options): Response
    {
        if ($this->accountSlug === null && str_contains($path, '{accountSlug}')) {
            throw new Exception('Account slug is not set. You must set it before calling this method.');
        }
        $this->authorization->reAuth();
        if ($this->authorization->getCredentials() === null) {
            throw new AuthorizationFailedException('Credentials are null');
        }
        $body = null;
        if (!empty($options['data'])) {
            try {
                $body = json_encode($options['data'], JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                throw new InvalidDataException('Failed to encode data to JSON', $exception->getCode(), $exception);
            }
        }

        $url = str_replace('{accountSlug}', $this->accountSlug ?? '', sprintf('%s%s', self::BASE_URL, $path));

        if (array_key_exists('params', $options) && $options['params'] !== []) {
            $url .= '?' . http_build_query($options['params']);
        }

        try {
            $request = new Request(
                $options['method'],
                $url,
                [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->authorization->getCredentials()->getAccessToken()
                ],
                $body
            );
            $response = new Response($this->client->sendRequest($request));
        } catch (ClientExceptionInterface $e) {
            throw new ConnectionFailedException($e->getMessage(), $e->getCode(), $e);
        }
        $responseStatusCode = $response->getStatusCode();
        if ($responseStatusCode >= 400 && $responseStatusCode < 500) {
            throw new ClientErrorException($request, $response);
        }
        if ($responseStatusCode >= 500 && $responseStatusCode < 600) {
            throw new ServerErrorException($request, $response);
        }
        return $response;
    }
}
