<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class AccountProviderIntegrationTest extends IntegrationTestCase
{
    public function testGetAccountInformation(): void
    {
        $response = $this->getManager()->getAccountProvider()->get();
        self::assertEquals(200, $response->getStatusCode());
        $account = $response->getBody();
        self::assertIsObject($account);
        self::assertObjectHasProperty('subdomain', $account);
        self::assertObjectHasProperty('plan', $account);
        self::assertEquals(getenv('FAKTUROID_ACCOUNT_SLUG'), $account->subdomain);
    }
}
