<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('integration')]
class RecurringGeneratorsProviderIntegrationTest extends IntegrationTestCase
{
    private static ?int $testSubjectId = null;
    private static ?int $testRecurringGeneratorId = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::assertNotNull(self::$manager);
        $response = self::$manager->getSubjectsProvider()->create([
            'name' => 'Invoice Test Subject ' . time(),
            'email' => 'invoice-test-' . time() . '@example.com'
        ]);
        $subject = $response->getBody();
        self::assertIsObject($subject);
        self::assertIsInt($subject->id);
        self::$testSubjectId = $subject->id;
    }

    public function testCreateRecurringGenerator(): void
    {
        $response = $this->getManager()->getRecurringGeneratorsProvider()->create([
            'name' => 'Test Recurring Generator ' . time(),
            'start_date' => date('Y-m-d'),
            'months_period' => 1,
            'subject_id' => self::$testSubjectId,
            'lines' => [
                [
                    'name' => 'Monthly Subscription',
                    'quantity' => 1,
                    'unit_price' => 500,
                    'vat_rate' => 21
                ]
            ]
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $generator = $response->getBody();
        self::assertIsObject($generator);
        self::assertObjectHasProperty('id', $generator);
        self::assertIsInt($generator->id);
        self::$testRecurringGeneratorId = $generator->id;
        self::assertObjectHasProperty('name', $generator);
        self::assertObjectHasProperty('subject_id', $generator);
        self::assertObjectHasProperty('months_period', $generator);
        self::assertObjectHasProperty('lines', $generator);
    }

    #[Depends('testCreateRecurringGenerator')]
    public function testListRecurringGenerators(): void
    {
        $response = $this->getManager()->getRecurringGeneratorsProvider()->list();

        self::assertEquals(200, $response->getStatusCode());

        $generators = $response->getBody(true);
        self::assertIsArray($generators);
        self::assertNotEmpty($generators);
        self::assertArrayHasKey(0, $generators);
        self::assertIsArray($generators[0]);
        self::assertArrayHasKey('id', $generators[0]);
        self::assertArrayHasKey('name', $generators[0]);
        self::assertArrayHasKey('months_period', $generators[0]);
    }

    #[Depends('testCreateRecurringGenerator')]
    public function testListRecurringGeneratorsWithFilters(): void
    {
        $response = $this->getManager()->getRecurringGeneratorsProvider()->list([
            'page' => 1
        ]);

        self::assertEquals(200, $response->getStatusCode());

        $generators = $response->getBody(true);
        self::assertIsArray($generators);
        self::assertNotEmpty($generators);
        self::assertArrayHasKey(0, $generators);
        self::assertIsArray($generators[0]);
        self::assertArrayHasKey('id', $generators[0]);
        self::assertArrayHasKey('name', $generators[0]);
        self::assertArrayHasKey('months_period', $generators[0]);
    }

    #[Depends('testCreateRecurringGenerator')]
    public function testGetSingleRecurringGenerator(): void
    {
        self::assertNotNull(self::$testRecurringGeneratorId);

        $response = $this->getManager()->getRecurringGeneratorsProvider()->get(self::$testRecurringGeneratorId);

        self::assertEquals(200, $response->getStatusCode());
        $generator = $response->getBody();

        self::assertIsObject($generator);
        self::assertObjectHasProperty('id', $generator);
        self::assertIsInt($generator->id);
        self::assertEquals(self::$testRecurringGeneratorId, $generator->id);
        self::assertObjectHasProperty('name', $generator);
        self::assertObjectHasProperty('months_period', $generator);
    }

    #[Depends('testGetSingleRecurringGenerator')]
    public function testUpdateRecurringGenerator(): void
    {
        self::assertNotNull(self::$testRecurringGeneratorId);

        $newName = 'Updated Recurring Generator ' . time();

        $response = $this->getManager()->getRecurringGeneratorsProvider()
            ->update(
                self::$testRecurringGeneratorId,
                ['name' => $newName]
            );

        self::assertEquals(200, $response->getStatusCode());

        $generator = $response->getBody();
        self::assertIsObject($generator);
        self::assertObjectHasProperty('name', $generator);
        self::assertEquals($newName, $generator->name);
    }

    #[Depends('testUpdateRecurringGenerator')]
    public function testPauseRecurringGenerator(): void
    {
        self::assertNotNull(self::$testRecurringGeneratorId);

        $response = $this->getManager()->getRecurringGeneratorsProvider()->pause(
            self::$testRecurringGeneratorId
        );

        self::assertEquals(200, $response->getStatusCode());

        $generator = $response->getBody();
        self::assertIsObject($generator);
        self::assertObjectHasProperty('active', $generator);
        self::assertFalse($generator->active);
    }

    #[Depends('testPauseRecurringGenerator')]
    public function testActivateRecurringGenerator(): void
    {
        self::assertNotNull(self::$testRecurringGeneratorId);

        $response = $this->getManager()->getRecurringGeneratorsProvider()->activate(
            self::$testRecurringGeneratorId,
            ['start_date' => date('Y-m-d', strtotime('+1 day'))]
        );

        self::assertEquals(200, $response->getStatusCode());

        $generator = $response->getBody();
        self::assertIsObject($generator);
        self::assertObjectHasProperty('active', $generator);
        self::assertNotFalse($generator->active);
    }

    public function testDeleteRecurringGenerator(): void
    {
        $response = $this->getManager()->getRecurringGeneratorsProvider()->create([
            'name' => 'Test Recurring Generator ' . time(),
            'start_date' => date('Y-m-d'),
            'months_period' => 1,
            'subject_id' => self::$testSubjectId,
            'lines' => [
                [
                    'name' => 'Monthly Subscription',
                    'quantity' => 1,
                    'unit_price' => 500,
                    'vat_rate' => 21
                ]
            ]
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $generator = $response->getBody();
        self::assertIsObject($generator);
        self::assertObjectHasProperty('id', $generator);
        self::assertIsInt($generator->id);
        $response = $this->getManager()->getRecurringGeneratorsProvider()->delete($generator->id);

        self::assertEquals(204, $response->getStatusCode());
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$manager !== null) {
            if (self::$testRecurringGeneratorId !== null) {
                try {
                    self::$manager->getRecurringGeneratorsProvider()->delete(self::$testRecurringGeneratorId);
                } catch (\Exception) {
                    // Ignore cleanup errors
                }
            }

            if (self::$testSubjectId !== null) {
                try {
                    self::$manager->getSubjectsProvider()->delete(self::$testSubjectId);
                } catch (\Exception) {
                    // Ignore cleanup errors
                }
            }
        }

        parent::tearDownAfterClass();
    }
}
