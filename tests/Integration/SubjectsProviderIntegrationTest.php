<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class SubjectsProviderIntegrationTest extends IntegrationTestCase
{
    private static ?int $createdSubjectId = null;

    public function testCreateSubject(): void
    {
        $uniqueId = 'test-' . time() . '-' . random_int(1000, 9999);
        $response = $this->getManager()->getSubjectsProvider()->create([
            'name' => 'Integration Test Company',
            'email' => 'test-' . $uniqueId . '@example.com',
            'custom_id' => $uniqueId,
            'street' => 'Test Street 123',
            'city' => 'Prague',
            'zip' => '110 00',
            'country' => 'CZ'
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $subject = $response->getBody();
        self::assertIsObject($subject);
        self::assertObjectHasProperty('id', $subject);
        self::assertIsInt($subject->id);
        self::$createdSubjectId = $subject->id;
        self::assertObjectHasProperty('name', $subject);
        self::assertObjectHasProperty('email', $subject);
        self::assertObjectHasProperty('custom_id', $subject);
        self::assertObjectHasProperty('street', $subject);
        self::assertEquals('Integration Test Company', $subject->name);
        self::assertEquals($uniqueId, $subject->custom_id);
    }

    #[Depends('testCreateSubject')]
    public function testListSubjects(): void
    {
        $response = $this->getManager()->getSubjectsProvider()->list();
        self::assertEquals(200, $response->getStatusCode());
        $subjects = $response->getBody(true);
        self::assertIsArray($subjects);
        self::assertNotEmpty($subjects);
        self::assertArrayHasKey(0, $subjects);
        self::assertIsArray($subjects[0]);
        self::assertArrayHasKey('id', $subjects[0]);
        self::assertArrayHasKey('name', $subjects[0]);
    }

    #[Depends('testCreateSubject')]
    public function testGetSingleSubject(): void
    {
        self::assertNotNull(self::$createdSubjectId);
        $response = $this->getManager()->getSubjectsProvider()->get(self::$createdSubjectId);
        self::assertEquals(200, $response->getStatusCode());
        $subject = $response->getBody();
        self::assertIsObject($subject);
        self::assertObjectHasProperty('id', $subject);
        self::assertEquals(self::$createdSubjectId, $subject->id);
        self::assertObjectHasProperty('name', $subject);
        self::assertObjectHasProperty('email', $subject);
    }

    #[Depends('testGetSingleSubject')]
    public function testUpdateSubject(): void
    {
        self::assertNotNull(self::$createdSubjectId);

        $updatedName = 'Updated Company Name ' . time();
        $response = $this->getManager()->getSubjectsProvider()->update(
            self::$createdSubjectId,
            ['name' => $updatedName]
        );

        self::assertEquals(200, $response->getStatusCode());
        $subject = $response->getBody();
        self::assertIsObject($subject);
        self::assertObjectHasProperty('name', $subject);
        self::assertEquals($updatedName, $subject->name);
    }

    public function testSearchSubjects(): void
    {
        $response = $this->getManager()->getSubjectsProvider()->search([
            'query' => 'Integration Test'
        ]);

        self::assertEquals(200, $response->getStatusCode());
        $subjects = $response->getBody(true);
        self::assertIsArray($subjects);
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$createdSubjectId !== null && self::$manager !== null) {
            try {
                self::$manager->getSubjectsProvider()->delete(self::$createdSubjectId);
            } catch (\Exception) {
            }
        }

        parent::tearDownAfterClass();
    }
}
