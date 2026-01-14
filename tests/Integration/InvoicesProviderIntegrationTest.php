<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class InvoicesProviderIntegrationTest extends IntegrationTestCase
{
    private static ?int $testSubjectId = null;
    private static ?int $testInvoiceId = null;
    private static ?int $testPaymentId = null;
    private static ?int $testAttachmentId = null;
    private static string $examplePdfBase64;

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

        $pdfContent = file_get_contents(__DIR__ . '/../data/example.pdf');
        self::assertNotFalse($pdfContent);

        self::$examplePdfBase64 = base64_encode($pdfContent);
    }

    public function testCreateInvoice(): void
    {
        $response = $this->getManager()->getInvoicesProvider()->create([
            'subject_id' => self::$testSubjectId,
            'lines' => [
                [
                    'name' => 'Test Product',
                    'quantity' => 1,
                    'unit_price' => 100,
                    'vat_rate' => 21
                ]
            ],
            'attachments' => [
                [
                    'filename' => 'test-invoice.pdf',
                    'data_url' => 'data:application/pdf;base64,' . self::$examplePdfBase64
                ]
            ],
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $invoice = $response->getBody();
        self::assertIsObject($invoice);
        self::assertObjectHasProperty('id', $invoice);
        self::assertIsInt($invoice->id);
        self::$testInvoiceId = $invoice->id;
        self::assertObjectHasProperty('number', $invoice);
        self::assertObjectHasProperty('subject_id', $invoice);
        self::assertObjectHasProperty('lines', $invoice);
        self::assertObjectHasProperty('total', $invoice);
        self::assertEquals(self::$testSubjectId, $invoice->subject_id);
        self::assertIsArray($invoice->lines);
        self::assertCount(1, $invoice->lines);
        self::assertIsArray($invoice->attachments);
        self::assertCount(1, $invoice->attachments);
        self::assertArrayHasKey(0, $invoice->attachments);
        self::assertIsObject($invoice->attachments[0]);
        self::assertObjectHasProperty('filename', $invoice->attachments[0]);
        self::assertObjectHasProperty('id', $invoice->attachments[0]);
        self::assertEquals('test-invoice.pdf', $invoice->attachments[0]->filename);
        self::assertIsInt($invoice->attachments[0]->id);
        self::$testAttachmentId = $invoice->attachments[0]->id;
    }

    #[Depends('testCreateInvoice')]
    public function testListInvoices(): void
    {
        self::assertNotNull(self::$testSubjectId);
        $response = $this->getManager()->getInvoicesProvider()
            ->list([
                'document_type' => 'regular',
                'subject_id' => self::$testSubjectId
            ]);
        self::assertEquals(200, $response->getStatusCode());
        $invoices = $response->getBody(true);
        self::assertIsArray($invoices);
        self::assertCount(1, $invoices);
    }

    #[Depends('testCreateInvoice')]
    public function testGetSingleInvoice(): void
    {
        if (self::$testInvoiceId === null) {
            $this->fail('No invoice created');
        }

        $response = $this->getManager()->getInvoicesProvider()->get(self::$testInvoiceId);
        self::assertEquals(200, $response->getStatusCode());
        $invoice = $response->getBody();
        self::assertIsObject($invoice);
        self::assertObjectHasProperty('id', $invoice);
        self::assertEquals(self::$testInvoiceId, $invoice->id);
        self::assertObjectHasProperty('number', $invoice);
        self::assertObjectHasProperty('lines', $invoice);
    }

    #[Depends('testCreateInvoice')]
    public function testUpdateInvoice(): void
    {
        if (self::$testInvoiceId === null) {
            $this->fail('No invoice created');
        }

        $newNote = 'Updated note ' . time();
        $response = $this->getManager()->getInvoicesProvider()->update(
            self::$testInvoiceId,
            ['private_note' => $newNote]
        );

        self::assertEquals(200, $response->getStatusCode());
        $invoice = $response->getBody();
        self::assertIsObject($invoice);
        self::assertObjectHasProperty('private_note', $invoice);
        self::assertEquals($newNote, $invoice->private_note);
    }

    public function testSearchInvoices(): void
    {
        $response = $this->getManager()->getInvoicesProvider()->search([
            'query' => 'Test'
        ]);

        self::assertEquals(200, $response->getStatusCode());
        $invoices = $response->getBody(true);
        self::assertIsArray($invoices);
        self::assertNotEmpty($invoices);
    }

    #[Depends('testCreateInvoice')]
    public function testGetInvoicePdf(): void
    {
        if (self::$testInvoiceId === null) {
            $this->fail('No invoice created');
        }

        $response = $this->getManager()->getInvoicesProvider()->getPdf(self::$testInvoiceId);
        self::assertContains($response->getStatusCode(), [200, 204]);
    }

    #[Depends('testCreateInvoice')]
    public function testGetInvoiceAttachment(): void
    {
        if (self::$testInvoiceId === null || self::$testAttachmentId === null) {
            $this->fail('No invoice created');
        }
        $response = $this->getManager()->getInvoicesProvider()->getAttachment(
            self::$testInvoiceId,
            self::$testAttachmentId
        );

        self::assertEquals(200, $response->getStatusCode());
        $content = $response->getBody();
        self::assertIsString($content);
        self::assertNotEmpty($content);
    }

    #[Depends('testUpdateInvoice')]
    public function testFireActionLock(): void
    {
        $invoiceId = self::$testInvoiceId;
        if ($invoiceId === null) {
            $this->fail('No invoice created');
        }
        $invoiceProvider = $this->getManager()->getInvoicesProvider();

        $response = $invoiceProvider->fireAction($invoiceId, 'lock');
        self::assertEquals(204, $response->getStatusCode());

        $response = $invoiceProvider->fireAction($invoiceId, 'unlock');
        self::assertEquals(204, $response->getStatusCode());
    }

    #[Depends('testFireActionLock')]
    public function testCreatePayment(): void
    {
        self::assertNotNull(self::$testInvoiceId);

        $response = $this->getManager()->getInvoicesProvider()->createPayment(
            self::$testInvoiceId,
            [
                'paid_on' => date('Y-m-d'),
                'paid_amount' => 121
            ]
        );

        self::assertEquals(201, $response->getStatusCode());
        $invoicePayment = $response->getBody();
        self::assertIsObject($invoicePayment);
        self::assertObjectHasProperty('id', $invoicePayment);
        self::assertIsInt($invoicePayment->id);
        self::$testPaymentId = $invoicePayment->id;
    }

    #[Depends('testCreatePayment')]
    public function testCreateTaxDocument(): void
    {
        $this->markTestIncomplete('When we can create tax document?');
        self::assertNotNull(self::$testInvoiceId); // @phpstan-ignore-line deadCode.unreachable
        self::assertNotNull(self::$testPaymentId);
        $response = $this->getManager()->getInvoicesProvider()->createTaxDocument(
            self::$testInvoiceId,
            self::$testPaymentId,
            ['paid_on' => date('Y-m-d')]
        );

        self::assertEquals(201, $response->getStatusCode());
        $taxDocument = $response->getBody();
        self::assertIsObject($taxDocument);
        self::assertObjectHasProperty('id', $taxDocument);
    }

    #[Depends('testCreatePayment')]
    public function testDeletePaymentFromInvoice(): void
    {
        if (self::$testInvoiceId === null || self::$testPaymentId === null) {
            $this->fail('No invoice or payment created');
        }

        $response = $this->getManager()->getInvoicesProvider()->deletePayment(
            self::$testInvoiceId,
            self::$testPaymentId
        );

        self::assertEquals(204, $response->getStatusCode());
        self::$testPaymentId = null;
    }

    public function testSendMessage(): void
    {
        $response = $this->getManager()->getInvoicesProvider()->create([
            'subject_id' => self::$testSubjectId,
            'lines' => [
                [
                    'name' => 'Message Test Product',
                    'quantity' => 1,
                    'unit_price' => 50,
                    'vat_rate' => 21
                ]
            ]
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $invoice = $response->getBody();
        self::assertIsObject($invoice);
        self::assertObjectHasProperty('id', $invoice);
        self::assertIsInt($invoice->id);
        $invoiceId = $invoice->id;

        $messageResponse = $this->getManager()->getInvoicesProvider()->createMessage(
            $invoiceId,
            [
                'email' => 'trash@fakturoid.cz',
                'subject' => 'Test Invoice',
                'message' => 'Test message body',
                'deliver_now' => false,
            ]
        );

        self::assertEquals(204, $messageResponse->getStatusCode());
        $this->getManager()->getInvoicesProvider()->delete($invoiceId);
    }

    public function testDeleteInvoice(): void
    {
        $response = $this->getManager()->getInvoicesProvider()->create([
            'subject_id' => self::$testSubjectId,
            'lines' => [
                [
                    'name' => 'Delete Test Product',
                    'quantity' => 1,
                    'unit_price' => 50,
                    'vat_rate' => 21
                ]
            ]
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $invoice = $response->getBody();
        self::assertIsObject($invoice);
        self::assertObjectHasProperty('id', $invoice);
        self::assertIsInt($invoice->id);
        $deleteResponse = $this->getManager()->getInvoicesProvider()->delete($invoice->id);
        self::assertEquals(204, $deleteResponse->getStatusCode());
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$manager !== null) {
            if (self::$testInvoiceId !== null) {
                try {
                    self::$manager->getInvoicesProvider()->delete(self::$testInvoiceId);
                } catch (\Exception) {
                }
            }
            if (self::$testSubjectId !== null) {
                try {
                    self::$manager->getSubjectsProvider()->delete(self::$testSubjectId);
                } catch (\Exception) {
                }
            }
        }

        parent::tearDownAfterClass();
    }
}
