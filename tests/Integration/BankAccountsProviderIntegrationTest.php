<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class BankAccountsProviderIntegrationTest extends IntegrationTestCase
{
    public function testListBankAccounts(): void
    {
        $response = $this->getManager()->getBankAccountsProvider()->list();
        self::assertEquals(200, $response->getStatusCode());
        $accounts = $response->getBody(true);
        self::assertIsArray($accounts);
        self::assertNotEmpty($accounts);
        self::assertArrayHasKey(0, $accounts);
        self::assertIsArray($accounts[0]);
        self::assertArrayHasKey('id', $accounts[0]);
        self::assertArrayHasKey('name', $accounts[0]);
        self::assertArrayHasKey('number', $accounts[0]);
        self::assertArrayHasKey('currency', $accounts[0]);
    }
}
