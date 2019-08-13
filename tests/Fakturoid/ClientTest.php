<?php

use PHPUnit\Framework\TestCase;
use Fakturoid\Client as Client;

class ClientTest extends TestCase
{
    /* Account */

    public function testGetAccount()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $account = $f->getAccount();
    }

    public function testGetAccountWithHeaders()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $account = $f->getAccount(array(
            'If-None-Match'     => 'W/"e79a1fdf3cf010530b6d6827549915ce"',
            'If-Modified-Since' => 'Tue, 27 Mar 2018 12:40:03 GMT'
        ));
    }

    public function testGetAccountWithDateTimeHeader()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $account = $f->getAccount(array(
            'If-Modified-Since' => new DateTime
        ));
    }

    /**
     * @expectedException PHPUnit\Framework\Error\Notice
     */
    public function testGetAccountWithInvalidHeaders()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $account = $f->getAccount(array('Unknown' => 'Hello'));
    }

    /* User */

    public function testGetUser()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $user = $f->getUser(10);
    }

    public function testGetUsers()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $users = $f->getUsers();
    }

    /* Invoice */

    public function testGetInvoices()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->getInvoices();
    }

    public function testGetInvoicesSecondPage()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->getInvoices(array('page' => 2));
    }

    public function testGetRegularInvoices()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->getRegularInvoices();
    }

    public function testGetProformaInvoices()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->getProformaInvoices();
    }

    public function testGetInvoice()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoice = $f->getInvoice(86);
    }

    public function testGetInvoicePdf()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $pdf = $f->getInvoicePdf(86);
    }

    public function testSearchInvoices()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->searchInvoices(array('query' => 'Test'));
    }

    public function testUpdateInvoice()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoice = $f->updateInvoice(86, array('due' => 5));
    }

    public function testFireInvoice()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->fireInvoice(86, 'pay');
        $response = $f->fireInvoice(86, 'pay', array('paid_at' => '2018-03-21T00:00:00+01:00'));
        $response = $f->fireInvoice(86, 'pay', array('paid_at' => '2018-03-21T00:00:00+01:00', 'paid_amount' => '1000', 'variable_symbol' => '12345678', 'bank_account_id' => 23));
    }

    public function testCreateInvoice()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoice = $f->createInvoice(array(
            'subject_id' => 36,
            'lines' => array(
                array(
                    'quantity'   => 5,
                    'unit_name'  => 'kg',
                    'name'       => 'Sand',
                    'unit_price' => '100',
                    'vat_rate'   => 21
                )
            )
        ));
    }

    public function testDeleteInvoice()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->deleteInvoice(86);
    }

    /* Expense */

    public function testGetExpenses()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expenses = $f->getExpenses();
    }

    public function testGetExpense()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expense = $f->getExpense(201);
    }

    public function testSearchExpenses()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expenses = $f->searchExpenses(array('query' => 'Test'));
    }

    public function testUpdateExpense()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expense = $f->updateExpense(201, array('due' => 5));
    }

    public function testFireExpense()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->fireExpense(201, 'pay');
        $response = $f->fireExpense(201, 'pay', array('paid_on' => '2018-03-21', 'paid_amount' => '1000', 'variable_symbol' => '12345678', 'bank_account_id' => 23));
    }

    public function testCreateExpense()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expense = $f->createExpense(array(
            'subject_id' => 36,
            'lines' => array(
                array(
                    'quantity'   => 5,
                    'unit_name'  => 'kg',
                    'name'       => 'Sand',
                    'unit_price' => '100',
                    'vat_rate'   => 21
                )
            )
        ));
    }

    public function testDeleteExpense()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->deleteExpense(201);
    }

    /* Subject */

    public function testGetSubjects()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subjects = $f->getSubjects();
    }

    public function testGetSubject()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subject = $f->getSubject(36);
    }

    public function testCreateSubject()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subject = $f->createSubject(array('name' => 'Apple Czech s.r.o.'));
    }

    public function testUpdateSubject()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subject = $f->updateSubject(36, array('street' => 'Tetst'));
    }

    public function testDeleteSubject()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->deleteSubject(36);
    }

    public function testSearchSubjects()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subjects = $f->searchSubjects(array('query' => 'Apple'));
    }

    /* Generator */

    public function testGetGenerators()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generators = $f->getGenerators();
    }

    public function testGetTemplateGenerators()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generators = $f->getTemplateGenerators();
    }

    public function testGetRecurringGenerators()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generators = $f->getRecurringGenerators();
    }

    public function testGetGenerator()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generator = $f->getGenerator(10);
    }

    public function testCreateGenerator()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generator = $f->createGenerator(array(
            'name' => 'Test',
            'subject_id' => 36,
            'payment_method' => 'bank',
            'currency' => 'CZK',
            'lines' => array(
                array(
                    'quantity'   => 5,
                    'unit_name'  => 'kg',
                    'name'       => 'Sand',
                    'unit_price' => '100',
                    'vat_rate'   => 21
                )
            )
        ));
    }

    public function testUpdateGenerator()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generator = $f->updateGenerator(10, array('due' => 5));
    }

    public function testDeleteGenerator()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->deleteGenerator(10);
    }

    /* Message */

    public function testCreateMessage()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $message = $f->createMessage(86, array(
            'email' => 'test@example.org',
            'subject' => 'Hello',
            'message' => "Hello,\n\nI have invoice for you.\n#link#\n\n   John Doe"
        ));
    }

    /* Event */

    public function testGetEvents()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $events = $f->getEvents();
    }

    public function testGetPaidEvents()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $events = $f->getPaidEvents();
    }

    /* Todo */

    public function testGetTodos()
    {
        $requester = $this->createMock('Fakturoid\Requester');
        $requester->method('run')->willReturn(null);

        $f = new Client('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $todos = $f->getTodos();
    }
}
