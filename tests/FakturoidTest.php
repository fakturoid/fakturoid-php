<?php

use PHPUnit\Framework\TestCase;

class FakturoidTest extends TestCase
{
    /* Account */

    public function testGetAccount()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/account.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $account = $f->get_account();

        $this->assertEquals('bigtest', $account->subdomain);
    }

    /* User */

    public function testGetUser()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/user.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $user = $f->get_user(10);

        $this->assertEquals('Bořivoj Hejsek', $user->full_name);
    }

    public function testGetUsers()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/users.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $users = $f->get_users();

        $this->assertEquals('Martin Hejsek', $users[0]->full_name);
    }

    /* Invoice */

    public function testGetInvoices()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/invoices.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->get_invoices();

        $this->assertEquals('2018-0051', $invoices[0]->number);
    }

    public function testGetInvoicesSecondPage()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/invoices.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->get_invoices(array('page' => 2));

        $this->assertEquals('2018-0051', $invoices[0]->number);
    }

    public function testGetRegularInvoices()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/invoices.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->get_regular_invoices();

        $this->assertEquals('2018-0051', $invoices[0]->number);
    }

    public function testGetProformaInvoices()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/proforma_invoices.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->get_proforma_invoices();

        $this->assertEquals('1-2018-0002', $invoices[0]->number);
    }

    public function testGetInvoice()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/invoice.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoice = $f->get_invoice(86);

        $this->assertEquals('2018-0051', $invoice->number);
    }

    public function testGetInvoicePdf()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/invoice.pdf'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $pdf = $f->get_invoice_pdf(86);

        $this->assertEquals(36290, strlen($pdf));
    }

    public function testSearchInvoices()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/search_invoices.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->search_invoices(array('query' => 'Test'));

        $this->assertEquals('2018-1006', $invoices[0]->number);
    }

    public function testUpdateInvoice()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/invoice.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoice = $f->update_invoice(86, array('due' => 5));

        $this->assertEquals('2018-0051', $invoice->number);
    }

    public function testFireInvoice()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->fire_invoice(86, 'pay', array('paid_at' => '2018-03-21T00:00:00+01:00'));

        $this->assertNull($response);
    }

    public function testCreateInvoice()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/invoice.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoice = $f->create_invoice(array(
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

        $this->assertEquals('2018-0051', $invoice->number);
    }

    public function testDeleteInvoice()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->delete_invoice(86);

        $this->assertNull($response);
    }

    /* Expense */

    public function testGetExpenses()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/expenses.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expenses = $f->get_expenses();

        $this->assertEquals('N20180307', $expenses[0]->number);
    }

    public function testGetExpense()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/expense.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expense = $f->get_expense(201);

        $this->assertEquals('N20180307', $expense->number);
    }

    public function testSearchExpenses()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/search_expenses.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expenses = $f->search_expenses(array('query' => 'Test'));

        $this->assertEquals('N20180307', $expenses[0]->number);
    }

    public function testUpdateExpense()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/expense.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expense = $f->update_expense(201, array('due' => 5));

        $this->assertEquals('N20180307', $expense->number);
    }

    public function testFireExpense()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->fire_expense(201, 'pay', array('paid_at' => '2018-03-21T00:00:00+01:00'));

        $this->assertNull($response);
    }

    public function testCreateExpense()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/expense.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expense = $f->create_expense(array(
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

        $this->assertEquals('N20180307', $expense->number);
    }

    public function testDeleteExpense()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->delete_expense(201);

        $this->assertNull($response);
    }

    /* Subject */

    public function testGetSubjects()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/subjects.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subjects = $f->get_subjects();

        $this->assertEquals('Apple Czech s.r.o.', $subjects[0]->name);
    }

    public function testGetSubject()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/subject.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subject = $f->get_subject(36);

        $this->assertEquals('Apple Czech s.r.o.', $subject->name);
    }

    public function testCreateSubject()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/subject.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subject = $f->create_subject(array('name' => 'Apple Czech s.r.o.'));

        $this->assertEquals('Apple Czech s.r.o.', $subject->name);
    }

    public function testUpdateSubject()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/subject.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subject = $f->update_subject(36, array('street' => 'Tetst'));

        $this->assertEquals('Apple Czech s.r.o.', $subject->name);
    }

    public function testDeleteSubject()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->delete_subject(36);

        $this->assertNull($response);
    }

    public function testSearchSubjects()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/subjects.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subjects = $f->search_subjects(array('query' => 'Apple'));

        $this->assertEquals('Apple Czech s.r.o.', $subjects[0]->name);
    }

    /* Generator */

    public function testGetGenerators()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/generators.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generators = $f->get_generators();

        $this->assertEquals('Test', $generators[0]->name);
    }

    public function testGetTemplateGenerators()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/generators.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generators = $f->get_template_generators();

        $this->assertEquals('Test', $generators[0]->name);
    }

    public function testGetRecurringGenerators()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/recurring_generators.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generators = $f->get_recurring_generators();

        $this->assertEquals('Test', $generators[0]->name);
    }

    public function testGetGenerator()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/generator.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generator = $f->get_generator(10);

        $this->assertEquals('Test', $generator->name);
    }

    public function testCreateGenerator()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/generator.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generator = $f->create_generator(array(
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

        $this->assertEquals('Test', $generator->name);
    }

    public function testUpdateGenerator()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/generator.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generator = $f->update_generator(10, array('due' => 5));

        $this->assertEquals('Test', $generator->name);
    }

    public function testDeleteGenerator()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->delete_generator(10);

        $this->assertNull($response);
    }

    /* Message */

    public function testCreateMessage()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/message.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $message = $f->create_message(86, array(
            'email' => 'test@example.org',
            'subject' => 'Hello',
            'message' => "Hello,\n\nI have invoice for you.\n#link#\n\n   John Doe"
        ));

        $this->assertEquals('Message created.', $message->status);
    }

    /* Event */

    public function testGetEvents()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/events.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $events = $f->get_events();

        $this->assertEquals('sent', $events[0]->name);
    }

    public function testGetPaidEvents()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/paid_events.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $events = $f->get_paid_events();

        $this->assertEquals('paid', $events[0]->name);
    }

    /* Todo */

    public function testGetTodos()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(file_get_contents('tests/fixtures/todos.json'));

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $todos = $f->get_todos();

        $this->assertEquals('account_exceeded_vat_turnover_limit', $todos[0]->name);
    }
}
