<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('integration')]
class InventoryItemsProviderIntegrationTest extends IntegrationTestCase
{
    private static ?int $testItemId = null;

    #[Test]
    public function testCreateInventoryItem(): void
    {
        $uniqueSku = 'SKU-' . time();

        $response = $this->getManager()->getInventoryItemsProvider()->create([
            'name' => 'Integration Test Item',
            'sku' => $uniqueSku,
            'track_quantity' => true,
            'quantity' => 10,
            'native_purchase_price' => 100,
            'native_retail_price' => 200
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $item = $response->getBody();
        self::assertIsObject($item);
        self::assertObjectHasProperty('id', $item);
        self::assertIsInt($item->id);
        self::$testItemId = $item->id;
        self::assertObjectHasProperty('id', $item);
        self::assertObjectHasProperty('name', $item);
        self::assertObjectHasProperty('sku', $item);
        self::assertEquals($uniqueSku, $item->sku);
    }

    #[Depends('testCreateInventoryItem')]
    public function testListInventoryItems(): void
    {
        $response = $this->getManager()->getInventoryItemsProvider()->list();

        self::assertEquals(200, $response->getStatusCode());

        $items = $response->getBody(true);
        self::assertIsArray($items);
        self::assertArrayHasKey(0, $items);
        self::assertIsArray($items[0]);
        self::assertArrayHasKey('id', $items[0]);
        self::assertArrayHasKey('name', $items[0]);
    }

    #[Depends('testCreateInventoryItem')]
    public function testGetSingleInventoryItem(): void
    {
        if (self::$testItemId === null) {
            $this->fail('No item created');
        }

        $response = $this->getManager()->getInventoryItemsProvider()->get(self::$testItemId);

        self::assertEquals(200, $response->getStatusCode());

        $item = $response->getBody();
        self::assertIsObject($item);
        self::assertObjectHasProperty('id', $item);
        self::assertIsInt($item->id);
        self::assertEquals(self::$testItemId, $item->id);
    }

    #[Depends('testCreateInventoryItem')]
    public function testListArchivedInventoryItems(): void
    {
        $response = $this->getManager()->getInventoryItemsProvider()->listArchived();

        self::assertEquals(200, $response->getStatusCode());

        $items = $response->getBody(true);
        self::assertIsArray($items);
        self::assertArrayHasKey(0, $items);
    }

    #[Depends('testCreateInventoryItem')]
    public function testListLowQuantityItems(): void
    {
        $response = $this->getManager()->getInventoryItemsProvider()->listLowQuantity();

        self::assertEquals(200, $response->getStatusCode());

        $items = $response->getBody(true);
        self::assertIsArray($items);
    }

    #[Depends('testCreateInventoryItem')]
    public function testUpdateInventoryItem(): void
    {
        if (self::$testItemId === null) {
            $this->fail('No item created');
        }

        $response = $this->getManager()->getInventoryItemsProvider()->update(
            self::$testItemId,
            ['name' => 'Updated Item Name']
        );

        self::assertEquals(200, $response->getStatusCode());

        $item = $response->getBody();
        self::assertIsObject($item);
        self::assertObjectHasProperty('name', $item);
        self::assertEquals('Updated Item Name', $item->name);
    }

    #[Test]
    #[Depends('testCreateInventoryItem')]
    public function testSearchInventoryItems(): void
    {
        $response = $this->getManager()->getInventoryItemsProvider()->search([
            'query' => 'Integration'
        ]);

        self::assertEquals(200, $response->getStatusCode());

        $items = $response->getBody(true);
        self::assertIsArray($items);
        self::assertArrayHasKey(0, $items);
    }

    #[Test]
    #[Depends('testCreateInventoryItem')]
    public function testArchiveInventoryItem(): void
    {
        if (self::$testItemId === null) {
            $this->fail('No item created');
        }

        $response = $this->getManager()->getInventoryItemsProvider()->archive(self::$testItemId);

        self::assertEquals(200, $response->getStatusCode());

        $item = $response->getBody();
        self::assertIsObject($item);
        self::assertObjectHasProperty('archived', $item);
        self::assertEquals(true, $item->archived);
    }

    #[Depends('testArchiveInventoryItem')]
    public function testUnArchiveInventoryItem(): void
    {
        if (self::$testItemId === null) {
            $this->fail('No item created');
        }

        $response = $this->getManager()->getInventoryItemsProvider()->unArchive(self::$testItemId);

        self::assertEquals(200, $response->getStatusCode());

        $item = $response->getBody();
        self::assertIsObject($item);
        self::assertObjectHasProperty('archived', $item);
        self::assertEquals(false, $item->archived);
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$manager !== null && self::$testItemId !== null) {
            try {
                self::$manager->getInventoryItemsProvider()->delete(self::$testItemId);
            } catch (\Exception) {
            }
        }

        parent::tearDownAfterClass();
    }
}
