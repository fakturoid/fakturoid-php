<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class WebhooksProviderIntegrationTest extends IntegrationTestCase
{
    private static ?int $testWebhookId = null; // @phpstan-ignore-line property.unusedType

    public function testCreateWebhook(): void
    {
        $this->markTestSkipped('Webhooks must be allowed in the account settings');
        $uniqueUrl = 'https://example.com/webhook-' . time(); // @phpstan-ignore-line deadCode.unreachable

        $response = $this->getManager()->getWebhooksProvider()->create([
            'url' => $uniqueUrl,
            'event' => 'invoice_created'
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $webhook = $response->getBody();
        self::assertIsObject($webhook);
        self::assertObjectHasProperty('id', $webhook);
        self::assertObjectHasProperty('url', $webhook);
        self::assertObjectHasProperty('event', $webhook);
        self::assertEquals($uniqueUrl, $webhook->url);
        self::assertIsInt($webhook->id);
        self::$testWebhookId = $webhook->id;
    }

    #[Depends('testCreateWebhook')]
    public function testListWebhooks(): void
    {
        $this->markTestSkipped('Webhooks must be allowed in the account settings');
        $response = $this->getManager()->getWebhooksProvider()->list(); // @phpstan-ignore-line deadCode.unreachable

        self::assertEquals(200, $response->getStatusCode());

        $webhooks = $response->getBody(true);
        self::assertIsArray($webhooks);
        self::assertNotEmpty($webhooks);
        self::assertArrayHasKey(0, $webhooks);
        self::assertArrayHasKey('id', $webhooks[0]);
        self::assertArrayHasKey('url', $webhooks[0]);
        self::assertArrayHasKey('event', $webhooks[0]);
    }

    #[Depends('testCreateWebhook')]
    public function testGetSingleWebhook(): void
    {
        $this->markTestSkipped('Webhooks must be allowed in the account settings');
        self::assertNotEmpty(self::$testWebhookId); // @phpstan-ignore-line deadCode.unreachable

        $response = $this->getManager()->getWebhooksProvider()->get(self::$testWebhookId);

        self::assertEquals(200, $response->getStatusCode());

        $webhook = $response->getBody();
        self::assertEquals(self::$testWebhookId, $webhook->id);
    }

    #[Depends('testCreateWebhook')]
    public function testUpdateWebhook(): void
    {
        self::assertNotNull(self::$testWebhookId);

        $newUrl = 'https://example.com/webhook-updated-' . time();

        $response = $this->getManager()->getWebhooksProvider()->update(
            self::$testWebhookId,
            ['url' => $newUrl]
        );

        self::assertEquals(200, $response->getStatusCode());

        $webhook = $response->getBody();
        self::assertIsObject($webhook);
        self::assertObjectHasProperty('url', $webhook);
        self::assertEquals($newUrl, $webhook->url);
    }

    #[Depends('testUpdateWebhook')]
    public function testDeleteWebhook(): void
    {
        self::assertNotNull(self::$testWebhookId);

        $response = $this->getManager()->getWebhooksProvider()
            ->delete(self::$testWebhookId);

        self::assertEquals(204, $response->getStatusCode());

        self::$testWebhookId = null;
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$manager !== null && self::$testWebhookId !== null) {
            try {
                self::$manager->getWebhooksProvider()->delete(self::$testWebhookId);
            } catch (\Exception) {
            }
        }

        parent::tearDownAfterClass();
    }
}
