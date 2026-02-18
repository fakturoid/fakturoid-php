<?php

namespace Fakturoid\Auth;

use Fakturoid\Enum\AuthTypeEnum;
use InvalidArgumentException;

class AccessToken
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $tokenType,
        public readonly int $expiresIn,
        public readonly ?string $refreshToken,
        public readonly ?string $scope,
        public readonly AuthTypeEnum $authType
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @param AuthTypeEnum $authType
     * @return self
     * @throws InvalidArgumentException
     */
    public static function create(array $data, AuthTypeEnum $authType): self
    {
        $accessToken = $data['access_token'] ?? null;
        if (!is_string($accessToken) || $accessToken === '') {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid or missing "access_token". Expected string, got %s.',
                    get_debug_type($accessToken)
                )
            );
        }

        $tokenType = $data['token_type'] ?? 'Bearer';
        if (!is_string($tokenType)) {
            throw new InvalidArgumentException(
                sprintf('Invalid "token_type". Expected string, got %s.', get_debug_type($tokenType))
            );
        }

        $expiresIn = $data['expires_in'] ?? 0;
        if (!is_int($expiresIn) && !is_numeric($expiresIn)) {
            throw new InvalidArgumentException(
                sprintf('Invalid "expires_in". Expected integer, got %s.', get_debug_type($expiresIn))
            );
        }
        $expiresIn = (int)$expiresIn;

        $scope = $data['scope'] ?? null;
        if ($scope !== null && !is_string($scope)) {
            throw new InvalidArgumentException(
                sprintf('Invalid "scope". Expected string or null, got %s.', get_debug_type($scope))
            );
        }

        $rawRefreshToken = $data['refresh_token'] ?? null;
        $finalRefreshToken = null;

        if ($authType === AuthTypeEnum::AUTHORIZATION_CODE_FLOW) {
            if (!is_string($rawRefreshToken) || $rawRefreshToken === '') {
                throw new InvalidArgumentException(
                    'Refresh token is required and must be a string for Authorization Code Flow.'
                );
            }
            $finalRefreshToken = $rawRefreshToken;
        }
        return new self($accessToken, $tokenType, $expiresIn, $finalRefreshToken, $scope, $authType);
    }
}
