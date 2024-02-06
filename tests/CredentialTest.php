<?php

namespace Fakturoid\Tests;

use DateTimeImmutable;
use DateTimeInterface;
use Fakturoid\Auth\Credentials;
use Fakturoid\Enum\AuthTypeEnum;

class CredentialTest extends TestCase
{
    public function testCredentials(): void
    {
        $dateTime = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2021-01-01T00:00:00+00:00');
        $this->assertNotFalse($dateTime);
        $credentials = new Credentials(
            'refresh_token',
            'access_token',
            $dateTime,
            AuthTypeEnum::AUTHORIZATION_CODE_FLOW
        );

        $this->assertEquals('access_token', $credentials->getAccessToken());
        $this->assertEquals('refresh_token', $credentials->getRefreshToken());
        $this->assertEquals(AuthTypeEnum::AUTHORIZATION_CODE_FLOW->value, $credentials->getAuthType()->value);

        $this->assertTrue($credentials->isExpired());
        $this->assertEquals(
            $dateTime->format(DateTimeInterface::ATOM),
            $credentials->getExpireAt()->format(DateTimeInterface::ATOM)
        );
        $this->assertEquals(
            '{"refreshToken":"refresh_token","accessToken":"access_token","expireAt":"2021-01-01T00:00:00+00:00","authType":"authorization_code"}', //@phpcs:ignore Generic.Files.LineLength
            $credentials->toJson()
        );

        $credentials->setAuthType(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW);
        $this->assertEquals(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW->value, $credentials->getAuthType()->value);
    }

    public function testSwitchAuthType(): void
    {
        $credentials = new Credentials(
            'refresh_token',
            'access_token',
            new DateTimeImmutable(),
            AuthTypeEnum::AUTHORIZATION_CODE_FLOW
        );
        $credentials->setAuthType(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW);
        $this->assertEquals(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW->value, $credentials->getAuthType()->value);
    }

    public function testExpiration(): void
    {
        $expireAt = (new DateTimeImmutable())->modify('+ 7200 seconds');
        $credentials = new Credentials(
            'refresh_token',
            'access_token',
            $expireAt,
            AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW
        );
        $this->assertFalse($credentials->isExpired());

        $expireAt = (new DateTimeImmutable())->modify('-10 seconds');
        $credentials = new Credentials(
            'refresh_token',
            'access_token',
            $expireAt,
            AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW
        );

        $this->assertTrue($credentials->isExpired());
    }
}
