<?php

declare(strict_types=1);

namespace fakturoid\fakturoid_php\Test;

use Carbon\Carbon;
use Exception;
use fakturoid\fakturoid_php\Client as Client;
use fakturoid\fakturoid_php\Requester;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


class ClientTest extends TestCase
{
    protected MockObject $requester;

    /* Account */

    public function testGetAccount()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getAccount();
    }

    public function testGetAccountWithHeaders()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getAccount(
            [
                'If-None-Match'     => 'W/"e79a1fdf3cf010530b6d6827549915ce"',
                'If-Modified-Since' => 'Tue, 27 Mar 2018 12:40:03 GMT'
            ]
        );
    }

    public function testGetAccountWithDateTimeHeader()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getAccount(
            [
                'If-Modified-Since' => Carbon::now()
            ]
        );
    }

    /**
     * @expectedException \PHPUnit\Framework\Error\Notice
     */
    public function testGetAccountWithInvalidHeaders()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );

        try {
            $f->getAccount(['Unknown' => 'Hello']);
        } catch (Exception $e) {
            self::assertStringContainsString('Unknown option keys: unknown', $e->getMessage());
        }
    }

    public function testGetUser()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getUser(10);
    }

    /* User */

    public function testGetUsers()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getUsers();
    }

    public function testGetInvoices()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getInvoices();
    }

    /* Invoice */

    public function testGetInvoicesSecondPage()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getInvoices(['page' => 2]);
    }

    public function testGetRegularInvoices()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getRegularInvoices();
    }

    public function testGetProformaInvoices()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getProformaInvoices();
    }

    public function testGetInvoice()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getInvoice(86);
    }

    public function testGetInvoicePdf()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getInvoicePdf(86);
    }

    public function testSearchInvoices()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->searchInvoices(['query' => 'Test']);
    }

    public function testUpdateInvoice()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->updateInvoice(86, ['due' => 5]);
    }

    public function testFireInvoice()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->fireInvoice(86, 'pay');
        $f->fireInvoice(86, 'pay', ['paid_at' => '2018-03-21T00:00:00+01:00']);
        $f->fireInvoice(
            86,
            'pay',
            [
                'paid_at'         => '2018-03-21T00:00:00+01:00',
                'paid_amount'     => '1000',
                'variable_symbol' => '12345678',
                'bank_account_id' => 23
            ]
        );
    }

    public function testCreateInvoice()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->createInvoice(
            [
                'subject_id' => 36,
                'lines'      => [
                    [
                        'quantity'   => 5,
                        'unit_name'  => 'kg',
                        'name'       => 'Sand',
                        'unit_price' => '100',
                        'vat_rate'   => 21

                    ]
                ],
            ]
        );
    }

    public function testDeleteInvoice()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->deleteInvoice(86);
    }

    public function testGetExpenses()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getExpenses();
    }

    /* Expense */

    public function testGetExpense()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getExpense(201);
    }

    public function testSearchExpenses()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->searchExpenses(['query' => 'Test']);
    }

    public function testUpdateExpense()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->updateExpense(201, ['due' => 5]);
    }

    public function testFireExpense()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->fireExpense(201, 'pay');
        $f->fireExpense(
            201,
            'pay',
            [
                'paid_on'         => '2018-03-21',
                'paid_amount'     => '1000',
                'variable_symbol' => '12345678',
                'bank_account_id' => 23
            ],
        );
    }

    public function testCreateExpense()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->createExpense(
            [
                'subject_id' => 36,
                'lines'      => [
                    [
                        'quantity'   => 5,
                        'unit_name'  => 'kg',
                        'name'       => 'Sand',
                        'unit_price' => '100',
                        'vat_rate'   => 21
                    ]
                ]
            ]

        );
    }

    public function testDeleteExpense()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->deleteExpense(201);
    }

    public function testGetSubjects()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getSubjects();
    }

    /* Subject */

    public function testGetSubject()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getSubject(36);
    }

    public function testCreateSubject()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->createSubject(['name' => 'Apple Czech s.r.o.']);
    }

    public function testUpdateSubject()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->updateSubject(36, ['street' => 'Tetst']);
    }

    public function testDeleteSubject()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->deleteSubject(36);
    }

    public function testSearchSubjects()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->searchSubjects(['query' => 'Apple']);
    }

    public function testGetGenerators()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getGenerators();
    }

    /* Generator */

    public function testGetTemplateGenerators()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getTemplateGenerators();
    }

    public function testGetRecurringGenerators()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getRecurringGenerators();
    }

    public function testGetGenerator()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getGenerator(10);
    }

    public function testCreateGenerator()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->createGenerator(
            [
                'name'           => 'Test',
                'subject_id'     => 36,
                'payment_method' => 'bank',
                'currency'       => 'CZK',
                'lines'          => [
                    [
                        'quantity'   => 5,
                        'unit_name'  => 'kg',
                        'name'       => 'Sand',
                        'unit_price' => '100',
                        'vat_rate'   => 21
                    ]
                ]
            ]
        );
    }

    public function testUpdateGenerator()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->updateGenerator(10, ['due' => 5]);
    }

    public function testDeleteGenerator()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->deleteGenerator(10);
    }

    public function testCreateMessage()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->createMessage(
            86,
            [
                'email'   => 'test@example.org',
                'subject' => 'Hello',
                'message' => "Hello,\n\nI have invoice for you.\n#link#\n\n   John Doe"
            ]
        );
    }

    /* Message */

    public function testGetEvents()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getEvents();
    }

    /* Event */

    public function testGetPaidEvents()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test',
            'test@example.org',
            'api-key',
            'Test <test@example.org>',
            ['requester' => $this->requester]
        );
        $f->getPaidEvents();
    }

    public function testGetTodos()
    {
        $this->requester->method('run')->willReturn(null);

        $f = new Client(
            'test', 'test@example.org', 'api-key',
            'Test <test@example.org>', ['requester' => $this->requester]
        );
        $f->getTodos();
    }

    /* Todo */

    protected function setUp(): void
    {
        parent::setUp();
        $this->requester = $this->createMock(Requester::class);
    }
}
