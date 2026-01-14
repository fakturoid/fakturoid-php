<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class EventsProviderIntegrationTest extends IntegrationTestCase
{
    public function testListEvents(): void
    {
        $response = $this->getManager()->getEventsProvider()->list();
        self::assertEquals(200, $response->getStatusCode());
        $events = $response->getBody(true);
        self::assertIsArray($events);
        self::assertNotEmpty($events);
        self::assertArrayHasKey(0, $events);
        self::assertIsArray($events[0]);
        self::assertArrayHasKey('name', $events[0]);
        self::assertArrayHasKey('created_at', $events[0]);
        self::assertArrayHasKey('text', $events[0]);
    }

    public function testListEventsWithPagination(): void
    {
        $response = $this->getManager()->getEventsProvider()->list(['page' => 1]);
        self::assertEquals(200, $response->getStatusCode());
        $events = $response->getBody(true);
        self::assertIsArray($events);
    }

    public function testListPaidEvents(): void
    {
        $response = $this->getManager()->getEventsProvider()->listPaid();
        self::assertEquals(200, $response->getStatusCode());
        $events = $response->getBody(true);
        self::assertIsArray($events);
        self::assertNotEmpty($events);
        self::assertArrayHasKey(0, $events);
        self::assertIsArray($events[0]);
        self::assertArrayHasKey('name', $events[0]);
        self::assertArrayHasKey('created_at', $events[0]);
        self::assertArrayHasKey('text', $events[0]);
    }
}
