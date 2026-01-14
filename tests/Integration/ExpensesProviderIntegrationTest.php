<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class ExpensesProviderIntegrationTest extends IntegrationTestCase
{
    private static ?int $testSubjectId = null;
    private static ?int $testExpenseId = null;
    private static ?int $testPaymentId = null;
    private static ?int $testAttachmentId = null;
    private static string $examplePdfBase64;

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
        $pdfContent = file_get_contents(__DIR__ . '/../data/example.pdf');
        self::assertNotFalse($pdfContent);

        self::$examplePdfBase64 = base64_encode($pdfContent);
    }

    public function testCreateExpense(): void
    {
        $response = $this->getManager()->getExpensesProvider()->create([
            'subject_id' => self::$testSubjectId,
            'lines' => [
                [
                    'name' => 'Test Expense Item',
                    'quantity' => 1,
                    'unit_price' => 500
                ]
            ],
            'attachments' => [
                [
                    'filename' => 'test-expense.pdf',
                    'data_url' => 'data:application/pdf;base64,' . self::$examplePdfBase64
                ]
            ],
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $expense = $response->getBody();
        self::assertIsObject($expense);
        self::assertObjectHasProperty('id', $expense);
        self::assertIsInt($expense->id);
        self::$testExpenseId = $expense->id;

        self::assertObjectHasProperty('number', $expense);
        self::assertObjectHasProperty('subject_id', $expense);
        self::assertObjectHasProperty('lines', $expense);
        self::assertObjectHasProperty('total', $expense);
        self::assertObjectHasProperty('attachments', $expense);
        self::assertIsArray($expense->attachments);
        self::assertCount(1, $expense->attachments);
        self::assertArrayHasKey(0, $expense->attachments);
        self::assertIsObject($expense->attachments[0]);
        self::assertObjectHasProperty('id', $expense->attachments[0]);
        self::assertIsInt($expense->attachments[0]->id);
        self::$testAttachmentId = $expense->attachments[0]->id;
    }

    #[Depends('testCreateExpense')]
    public function testListExpenses(): void
    {
        $response = $this->getManager()->getExpensesProvider()->list();
        self::assertEquals(200, $response->getStatusCode());
        $expenses = $response->getBody(true);
        self::assertIsArray($expenses);
        self::assertNotEmpty($expenses);
    }

    #[Depends('testCreateExpense')]
    public function testGetSingleExpense(): void
    {
        if (self::$testExpenseId === null) {
            $this->fail('No expense created');
        }

        $response = $this->getManager()->getExpensesProvider()->get(self::$testExpenseId);
        self::assertEquals(200, $response->getStatusCode());

        $expense = $response->getBody();
        self::assertIsObject($expense);
        self::assertObjectHasProperty('id', $expense);
        self::assertIsInt($expense->id);
        self::assertEquals(self::$testExpenseId, $expense->id);
    }

    #[Depends('testCreateExpense')]
    public function testSearchExpenses(): void
    {
        $response = $this->getManager()->getExpensesProvider()->create([
            'subject_id' => self::$testSubjectId,
            'lines' => [
                [
                    'name' => 'Delete Test Expense',
                    'quantity' => 1,
                    'unit_price' => 100
                ]
            ]
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $expense = $response->getBody();
        self::assertIsObject($expense);
        self::assertObjectHasProperty('id', $expense);
        self::assertIsInt($expense->id);
        $response = $this->getManager()->getExpensesProvider()->search([
            'query' => 'Test'
        ]);

        self::assertEquals(200, $response->getStatusCode());

        $expenses = $response->getBody(true);
        self::assertIsArray($expenses);
        self::assertArrayHasKey(0, $expenses);
        $this->getManager()->getExpensesProvider()->delete($expense->id);
    }

    #[Depends('testCreateExpense')]
    public function testUpdateExpense(): void
    {
        if (self::$testExpenseId === null) {
            $this->fail('No expense created');
        }

        $response = $this->getManager()->getExpensesProvider()->update(
            self::$testExpenseId,
            ['description' => 'Updated note']
        );

        self::assertEquals(200, $response->getStatusCode());

        $expense = $response->getBody();
        self::assertIsObject($expense);
        self::assertObjectHasProperty('description', $expense);
        self::assertEquals('Updated note', $expense->description);
    }

    #[Depends('testCreateExpense')]
    public function testGetExpenseAttachment(): void
    {
        if (self::$testExpenseId === null || self::$testAttachmentId === null) {
            $this->fail('No expense or attachment created');
        }

        $response = $this->getManager()->getExpensesProvider()->getAttachment(
            self::$testExpenseId,
            self::$testAttachmentId
        );

        self::assertEquals(200, $response->getStatusCode());

        $content = $response->getBody();
        self::assertIsString($content);
        self::assertNotEmpty($content);
    }

    #[Depends('testCreateExpense')]
    public function testFireActionOnExpense(): void
    {
        $expenseId = self::$testExpenseId;
        self::assertNotNull($expenseId);

        $expensesProvider = $this->getManager()->getExpensesProvider();
        $response = $expensesProvider->fireAction($expenseId, 'lock');
        self::assertEquals(204, $response->getStatusCode());

        $response = $expensesProvider->fireAction($expenseId, 'unlock');
        self::assertEquals(204, $response->getStatusCode());
    }

    #[Depends('testFireActionOnExpense')]
    public function testCreatePaymentForExpense(): void
    {
        if (self::$testExpenseId === null) {
            $this->fail('No expense created');
        }

        $response = $this->getManager()->getExpensesProvider()->createPayment(
            self::$testExpenseId,
            [
                'paid_on' => date('Y-m-d'),
                'paid_amount' => 500
            ]
        );

        self::assertEquals(201, $response->getStatusCode());

        $expensePayment = $response->getBody();
        self::assertIsObject($expensePayment);
        self::assertObjectHasProperty('id', $expensePayment);
        self::assertIsInt($expensePayment->id);
        self::$testPaymentId = $expensePayment->id;
    }

    #[Depends('testCreatePaymentForExpense')]
    public function testDeletePaymentFromExpense(): void
    {
        if (self::$testExpenseId === null || self::$testPaymentId === null) {
            $this->fail('No expense or payment created');
        }

        $response = $this->getManager()->getExpensesProvider()->deletePayment(
            self::$testExpenseId,
            self::$testPaymentId
        );

        self::assertEquals(204, $response->getStatusCode());

        self::$testPaymentId = null;
    }

    public function testDeleteExpense(): void
    {
        $response = $this->getManager()->getExpensesProvider()->create([
            'subject_id' => self::$testSubjectId,
            'lines' => [
                [
                    'name' => 'Delete Test Expense',
                    'quantity' => 1,
                    'unit_price' => 100
                ]
            ]
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $expense = $response->getBody();
        self::assertIsObject($expense);
        self::assertObjectHasProperty('id', $expense);
        self::assertIsInt($expense->id);
        $deleteResponse = $this->getManager()->getExpensesProvider()->delete($expense->id);
        self::assertEquals(204, $deleteResponse->getStatusCode());
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$manager !== null) {
            if (self::$testExpenseId !== null) {
                try {
                    self::$manager->getExpensesProvider()->delete(self::$testExpenseId);
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
