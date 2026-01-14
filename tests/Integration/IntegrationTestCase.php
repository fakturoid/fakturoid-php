<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use Fakturoid\FakturoidManager;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected static ?FakturoidManager $manager = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $clientId = getenv('FAKTUROID_CLIENT_ID');
        $clientSecret = getenv('FAKTUROID_CLIENT_SECRET');
        $accountSlug = getenv('FAKTUROID_ACCOUNT_SLUG');

        $client = new Client([
            'headers' => ['User-Agent' => 'Fakturoid PHP Integration Tests']
        ]);

        self::$manager = new FakturoidManager(
            $client,
            $clientId ?: '',
            $clientSecret ?: '',
            $accountSlug ?: ''
        );

        // Authenticate
        self::$manager->authClientCredentials();
    }

    protected function getManager(): FakturoidManager
    {
        if (self::$manager === null) {
            $this->fail('Manager not initialized');
        }
        return self::$manager;
    }

    /**
     * Helper to create a test subject for use in tests
     */
    protected function createTestSubject(): int
    {
        $response = $this->getManager()->getSubjectsProvider()->create([
            'name' => 'Integration Test Subject ' . time(),
            'email' => 'integration-test-' . time() . '@example.com',
            'custom_id' => 'test-' . time()
        ]);
        $subject = $response->getBody();
        self::assertIsObject($subject);
        self::assertIsInt($subject->id);
        return $subject->id;
    }
}
