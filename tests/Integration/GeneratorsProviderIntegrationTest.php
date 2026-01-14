<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('integration')]
class GeneratorsProviderIntegrationTest extends IntegrationTestCase
{
    private static ?int $testSubjectId = null;
    private static ?int $testGeneratorId = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::assertNotNull(self::$manager);
        $response = self::$manager->getSubjectsProvider()->create([
            'name' => 'Expense Test Subject ' . time(),
            'email' => 'expense-test-' . time() . '@example.com'
        ]);
        $subject = $response->getBody();
        self::assertIsObject($subject);
        self::assertIsInt($subject->id);
        self::$testSubjectId = $subject->id;
    }

    public function testCreateGenerator(): void
    {
        $response = $this->getManager()->getGeneratorsProvider()->create([
            'subject_id' => self::$testSubjectId,
            'name' => 'Test Generator ' . time(),
            'lines' => [
                [
                    'name' => 'Monthly Service',
                    'quantity' => 1,
                    'unit_price' => 1000,
                    'vat_rate' => 21
                ]
            ]
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $generator = $response->getBody();
        self::assertIsObject($generator);
        self::assertObjectHasProperty('id', $generator);
        self::assertObjectHasProperty('id', $generator);
        self::assertObjectHasProperty('name', $generator);
        self::assertObjectHasProperty('subject_id', $generator);
        self::assertObjectHasProperty('lines', $generator);
        self::assertIsInt($generator->id);
        self::$testGeneratorId = $generator->id;
    }

    #[Depends('testCreateGenerator')]
    public function testListGenerators(): void
    {
        $response = $this->getManager()->getGeneratorsProvider()->list();
        self::assertEquals(200, $response->getStatusCode());
        $generators = $response->getBody(true);
        self::assertIsArray($generators);
        self::assertArrayHasKey(0, $generators);
        self::assertIsArray($generators[0]);
        self::assertArrayHasKey('id', $generators[0]);
        self::assertArrayHasKey('name', $generators[0]);
    }

    #[Depends('testCreateGenerator')]
    public function testListGeneratorsWithPagination(): void
    {

        $response = $this->getManager()->getGeneratorsProvider()->create([
            'subject_id' => self::$testSubjectId,
            'name' => 'Test Generator ' . time(),
            'lines' => [
                [
                    'name' => 'Monthly Service',
                    'quantity' => 1,
                    'unit_price' => 1000,
                    'vat_rate' => 21
                ]
            ]
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $generator = $response->getBody();
        self::assertIsObject($generator);
        self::assertObjectHasProperty('id', $generator);
        self::assertIsInt($generator->id);

        $response = $this->getManager()->getGeneratorsProvider()->list(['page' => 1]);

        self::assertEquals(200, $response->getStatusCode());

        $generators = $response->getBody(true);
        self::assertIsArray($generators);
        self::assertArrayHasKey(0, $generators);
        self::assertIsArray($generators[0]);
        self::assertArrayHasKey('id', $generators[0]);
        self::assertArrayHasKey('name', $generators[0]);
        $this->getManager()->getGeneratorsProvider()->delete($generator->id);
    }

    #[Depends('testCreateGenerator')]
    public function testGetSingleGenerator(): void
    {
        if (self::$testGeneratorId === null) {
            $this->fail('No generator created');
        }

        $response = $this->getManager()->getGeneratorsProvider()->get(self::$testGeneratorId);

        self::assertEquals(200, $response->getStatusCode());

        $generator = $response->getBody();

        self::assertIsObject($generator);
        self::assertObjectHasProperty('id', $generator);
        self::assertEquals(self::$testGeneratorId, $generator->id);
        self::assertObjectHasProperty('name', $generator);
        self::assertObjectHasProperty('lines', $generator);
    }

    #[Depends('testGetSingleGenerator')]
    public function testUpdateGenerator(): void
    {
        if (self::$testGeneratorId === null) {
            $this->fail('No generator created');
        }

        $newName = 'Updated Generator ' . time();

        $response = $this->getManager()->getGeneratorsProvider()->update(
            self::$testGeneratorId,
            ['name' => $newName]
        );

        self::assertEquals(200, $response->getStatusCode());

        $generator = $response->getBody();
        self::assertIsObject($generator);
        self::assertObjectHasProperty('name', $generator);
        self::assertEquals($newName, $generator->name);
    }

    #[Depends('testUpdateGenerator')]
    public function testDeleteGenerator(): void
    {
        $response = $this->getManager()->getGeneratorsProvider()->create([
            'subject_id' => self::$testSubjectId,
            'name' => 'Test Generator ' . time(),
            'lines' => [
                [
                    'name' => 'Monthly Service',
                    'quantity' => 1,
                    'unit_price' => 1000,
                    'vat_rate' => 21
                ]
            ]
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $generator = $response->getBody();
        self::assertIsObject($generator);
        self::assertObjectHasProperty('id', $generator);
        self::assertIsInt($generator->id);

        $response = $this->getManager()->getGeneratorsProvider()->delete($generator->id);

        self::assertEquals(204, $response->getStatusCode());
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$manager !== null) {
            if (self::$testGeneratorId !== null) {
                try {
                    self::$manager->getGeneratorsProvider()->delete(self::$testGeneratorId);
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
