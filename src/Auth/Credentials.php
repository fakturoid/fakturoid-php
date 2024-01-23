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
        private readonly ?string $refresh_token,
        private readonly ?string $access_token,
        private readonly DateTimeImmutable $expireAt,
        private AuthTypeEnum $authType
    ) {
    }

    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }

    public function getAccessToken(): ?string
    {
        return $this->access_token;
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
                'refresh_token' => $this->refresh_token,
                'access_token' => $this->access_token,
                'expireAt' => $this->expireAt->format(DateTimeInterface::ATOM),
                'authType' => $this->authType,
            ], JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidDataException('Failed to encode credentials to JSON', $exception->getCode(), $exception);
        }
        return $json;
    }
}
