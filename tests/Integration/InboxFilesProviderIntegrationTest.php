<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('integration')]
class InboxFilesProviderIntegrationTest extends IntegrationTestCase
{
    private static ?int $testInboxFileId = null;
    private static string $examplePdfBase64;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::assertNotNull(self::$manager);

        $pdfContent = file_get_contents(__DIR__ . '/../data/example.pdf');
        self::assertNotFalse($pdfContent);

        self::$examplePdfBase64 = base64_encode($pdfContent);
    }

    #[Test]
    public function testCreateInboxFile(): void
    {
        $response = $this->getManager()->getInboxFilesProvider()->create([
            'filename' => 'test-file-' . time() . '.pdf',
            'send_to_ocr' => false,
            'attachment' => 'data:application/pdf;base64,' . self::$examplePdfBase64
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $file = $response->getBody();
        self::assertIsObject($file);
        self::assertObjectHasProperty('id', $file);
        self::assertIsInt($file->id);
        self::$testInboxFileId = $file->id;

        self::assertObjectHasProperty('id', $file);
        self::assertObjectHasProperty('filename', $file);
        self::assertObjectHasProperty('send_to_ocr', $file);
    }

    #[Depends('testCreateInboxFile')]
    public function testListInboxFiles(): void
    {
        $response = $this->getManager()->getInboxFilesProvider()->list();

        self::assertEquals(200, $response->getStatusCode());

        $files = $response->getBody(true);
        self::assertIsArray($files);
        self::assertArrayHasKey(0, $files);
        self::assertIsArray($files[0]);
        self::assertArrayHasKey('id', $files[0]);
        self::assertArrayHasKey('filename', $files[0]);
        self::assertArrayHasKey('send_to_ocr', $files[0]);
    }

    #[Depends('testCreateInboxFile')]
    public function testDownloadInboxFile(): void
    {
        if (self::$testInboxFileId === null) {
            $this->fail('No inbox file created');
        }
        $response = $this->getManager()->getInboxFilesProvider()->download(self::$testInboxFileId);
        self::assertEquals(200, $response->getStatusCode());
        $content = $response->getBody();
        self::assertIsString($content);
    }

    public function testDeleteInboxFile(): void
    {
        $response = $this->getManager()->getInboxFilesProvider()->create([
            'filename' => 'test-file-' . time() . '.pdf',
            'send_to_ocr' => false,
            'attachment' => 'data:application/pdf;base64,' . self::$examplePdfBase64
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $file = $response->getBody();
        self::assertIsObject($file);
        self::assertObjectHasProperty('id', $file);
        self::assertIsInt($file->id);
        $response = $this->getManager()->getInboxFilesProvider()->delete($file->id);
        self::assertEquals(204, $response->getStatusCode());
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$manager !== null && self::$testInboxFileId !== null) {
            try {
                self::$manager->getInboxFilesProvider()->delete(self::$testInboxFileId);
            } catch (\Exception) {
                // Ignore cleanup errors
            }
        }

        parent::tearDownAfterClass();
    }
}
