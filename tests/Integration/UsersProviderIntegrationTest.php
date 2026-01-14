<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class UsersProviderIntegrationTest extends IntegrationTestCase
{
    public function testGetCurrentUser(): void
    {
        $response = $this->getManager()->getUsersProvider()->getCurrentUser();
        self::assertEquals(200, $response->getStatusCode());
        $user = $response->getBody();
        self::assertIsObject($user);
        self::assertObjectHasProperty('id', $user);
        self::assertObjectHasProperty('email', $user);
        self::assertObjectHasProperty('full_name', $user);
        self::assertObjectHasProperty('accounts', $user);
        self::assertIsArray($user->accounts);
        self::assertNotEmpty($user->accounts);
    }

    public function testListUsers(): void
    {
        $response = $this->getManager()->getUsersProvider()->list();
        self::assertEquals(200, $response->getStatusCode());
        $users = $response->getBody(true);
        self::assertIsArray($users);
        self::assertNotEmpty($users);
    }
}
