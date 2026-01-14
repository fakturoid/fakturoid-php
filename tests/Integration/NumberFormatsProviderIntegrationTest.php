<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class NumberFormatsProviderIntegrationTest extends IntegrationTestCase
{
    public function testListNumberFormats(): void
    {
        $response = $this->getManager()->getNumberFormatsProvider()->list();

        self::assertEquals(200, $response->getStatusCode());

        $formats = $response->getBody(true);
        self::assertIsArray($formats);
        self::assertNotEmpty($formats);
        self::assertArrayHasKey(0, $formats);
        self::assertIsArray($formats[0]);
        self::assertArrayHasKey('id', $formats[0]);
        self::assertArrayHasKey('format', $formats[0]);
    }
}
