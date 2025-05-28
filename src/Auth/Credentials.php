<?php

namespace Fakturoid\Auth;

use DateTimeImmutable;
use DateTimeInterface;
use Fakturoid\Enum\AuthTypeEnum;
use Fakturoid\Exception\InvalidDataException;
use JsonException;

class Credentials
{
    /**
     * @readonly
     */
    private ?string $refreshToken;
    /**
     * @readonly
     */
    private ?string $accessToken;
    /**
     * @readonly
     */
    private DateTimeImmutable $expireAt;
    private string $authType;

    /**
     * @param 'authorization_code'|'client_credentials' $authType
     */
    public function __construct(
        #[\SensitiveParameter]
        ?string $refreshToken,
        #[\SensitiveParameter]
        ?string $accessToken,
        DateTimeImmutable $expireAt,
        string $authType
    ) {
        $this->refreshToken = $refreshToken;
        $this->accessToken = $accessToken;
        $this->expireAt = $expireAt;
        $this->authType = $authType;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function isExpired(): bool
    {
        return (new DateTimeImmutable()) > $this->expireAt;
    }

    public function getAuthType(): string
    {
        return $this->authType;
    }

    /**
     * @param mixed $type
     */
    public function setAuthType($type): void
    {
        $this->authType = $type;
    }

    public function getExpireAt(): DateTimeImmutable
    {
        return $this->expireAt;
    }

    /**
     * @throws InvalidDataException
     */
    public function toJson(): string
    {
        try {
            $json = json_encode([
                'refreshToken' => $this->refreshToken,
                'accessToken' => $this->accessToken,
                'expireAt' => $this->expireAt->format(DateTimeInterface::ATOM),
                'authType' => $this->authType,
            ], JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidDataException('Failed to encode credentials to JSON', $exception->getCode(), $exception);
        }
        return $json;
    }
}
