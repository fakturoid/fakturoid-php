<?php

namespace Fakturoid\Auth;

use DateTimeImmutable;
use DateTimeInterface;
use Fakturoid\Enum\AuthTypeEnum;
use Fakturoid\Exception\InvalidDataException;
use JsonException;

class Credentials
{
    public function __construct(
        #[\SensitiveParameter] private readonly ?string $refreshToken,
        #[\SensitiveParameter] private readonly ?string $accessToken,
        private readonly DateTimeImmutable $expireAt,
        private AuthTypeEnum $authType
    ) {
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

    public function getAuthType(): AuthTypeEnum
    {
        return $this->authType;
    }

    public function setAuthType(AuthTypeEnum $type): void
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
