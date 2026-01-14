<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('integration')]
class InventoryMovesProviderIntegrationTest extends IntegrationTestCase
{
    private static ?int $testItemId = null;
    private static ?int $testMoveId = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::assertNotNull(self::$manager);

        $uniqueSku = 'SKU-' . time();
        $response = self::$manager->getInventoryItemsProvider()->create([
            'name' => 'Integration Test Item',
            'sku' => $uniqueSku,
            'track_quantity' => true,
            'quantity' => 10,
            'native_purchase_price' => 100,
            'native_retail_price' => 200
        ]);
        $item = $response->getBody();
        self::assertIsObject($item);
        self::assertIsInt($item->id);
        self::$testItemId = $item->id;
    }

    #[Test]
    public function testCreateInventoryMove(): void
    {
        self::assertNotNull(self::$testItemId);
        $response = $this->getManager()->getInventoryMovesProvider()->create(
            self::$testItemId,
            [
                'direction' => 'in',
                'moved_on' => date('Y-m-d'),
                'quantity_change' => 10,
                'purchase_price' => 20,
            ]
        );

        self::assertEquals(201, $response->getStatusCode());

        $move = $response->getBody();
        self::assertIsObject($move);
        self::assertObjectHasProperty('id', $move);
        self::assertIsInt($move->id);
        self::$testMoveId = $move->id;

        self::assertObjectHasProperty('direction', $move);
        self::assertObjectHasProperty('moved_on', $move);
        self::assertEquals('in', $move->direction);
    }

    #[Test]
    #[Depends('testCreateInventoryMove')]
    public function testListInventoryMoves(): void
    {
        $response = $this->getManager()->getInventoryMovesProvider()->list();

        self::assertEquals(200, $response->getStatusCode());

        $moves = $response->getBody(true);
        self::assertIsArray($moves);
        self::assertNotEmpty($moves);
    }

    #[Depends('testCreateInventoryMove')]
    public function testListInventoryMovesForItem(): void
    {
        self::assertNotNull(self::$testItemId);
        $response = $this->getManager()->getInventoryMovesProvider()->list([
            'inventory_item_id' => self::$testItemId
        ]);

        self::assertEquals(200, $response->getStatusCode());

        $moves = $response->getBody(true);
        self::assertIsArray($moves);
        self::assertNotEmpty($moves);
    }

    #[Depends('testCreateInventoryMove')]
    public function testGetSingleInventoryMove(): void
    {
        if (self::$testMoveId === null || self::$testItemId === null) {
            $this->fail('No inventory move created');
        }

        $response = $this->getManager()->getInventoryMovesProvider()->get(
            self::$testItemId,
            self::$testMoveId
        );

        self::assertEquals(200, $response->getStatusCode());

        $move = $response->getBody();

        self::assertIsObject($move);
        self::assertEquals(self::$testMoveId, $move->id);
        self::assertObjectHasProperty('direction', $move);
        self::assertObjectHasProperty('quantity_change', $move);
    }

    #[Depends('testGetSingleInventoryMove')]
    public function testUpdateInventoryMove(): void
    {
        if (self::$testMoveId === null || self::$testItemId === null) {
            $this->fail('No inventory move created');
        }

        $response = $this->getManager()->getInventoryMovesProvider()->update(
            self::$testItemId,
            self::$testMoveId,
            ['quantity_change' => 60]
        );

        self::assertEquals(200, $response->getStatusCode());

        $move = $response->getBody();
        self::assertIsObject($move);
        self::assertEquals(self::$testMoveId, $move->id);
        self::assertObjectHasProperty('quantity_change', $move);
        self::assertEquals(60, $move->quantity_change);
    }

    public function testDeleteInventoryMove(): void
    {
        $itemId = self::$testItemId;
        self::assertNotNull($itemId);
        $inventoryMovesProvider = $this->getManager()->getInventoryMovesProvider();

        $response = $inventoryMovesProvider->create(
            $itemId,
            [
                'direction' => 'in',
                'moved_on' => date('Y-m-d'),
                'quantity_change' => 10,
                'purchase_price' => 20,
            ]
        );

        self::assertEquals(201, $response->getStatusCode());

        $move = $response->getBody();
        self::assertIsObject($move);
        self::assertObjectHasProperty('id', $move);
        self::assertIsInt($move->id);

        $response = $inventoryMovesProvider->delete($itemId, $move->id);

        self::assertEquals(204, $response->getStatusCode());
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$manager !== null) {
            if (self::$testMoveId !== null && self::$testItemId !== null) {
                try {
                    self::$manager->getInventoryMovesProvider()->delete(self::$testItemId, self::$testMoveId);
                } catch (\Exception) {
                }
            }

            if (self::$testItemId !== null) {
                try {
                    self::$manager->getInventoryItemsProvider()->delete(self::$testItemId);
                } catch (\Exception) {
                }
            }
        }

        parent::tearDownAfterClass();
    }
}
