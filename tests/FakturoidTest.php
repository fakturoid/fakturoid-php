<?php

use PHPUnit\Framework\TestCase;

class FakturoidTest extends TestCase
{
    /* Account */

    public function testGetAccount()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $account = $f->get_account();
    }

    /* User */

    public function testGetUser()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $user = $f->get_user(10);
    }

    public function testGetUsers()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $users = $f->get_users();
    }

    /* Invoice */

    public function testGetInvoices()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->get_invoices();
    }

    public function testGetInvoicesSecondPage()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->get_invoices(array('page' => 2));
    }

    public function testGetRegularInvoices()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->get_regular_invoices();
    }

    public function testGetProformaInvoices()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->get_proforma_invoices();
    }

    public function testGetInvoice()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoice = $f->get_invoice(86);
    }

    public function testGetInvoicePdf()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $pdf = $f->get_invoice_pdf(86);
    }

    public function testSearchInvoices()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoices = $f->search_invoices(array('query' => 'Test'));
    }

    public function testUpdateInvoice()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $invoice = $f->update_invoice(86, array('due' => 5));
    }

    public function testFireInvoice()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->fire_invoice(86, 'pay', array('paid_at' => '2018-03-21T00:00:00+01:00'));
    }

    public function testCreateInvoice()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

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
    }

    public function testDeleteInvoice()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->delete_invoice(86);
    }

    /* Expense */

    public function testGetExpenses()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expenses = $f->get_expenses();
    }

    public function testGetExpense()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expense = $f->get_expense(201);
    }

    public function testSearchExpenses()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expenses = $f->search_expenses(array('query' => 'Test'));
    }

    public function testUpdateExpense()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $expense = $f->update_expense(201, array('due' => 5));
    }

    public function testFireExpense()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->fire_expense(201, 'pay', array('paid_at' => '2018-03-21T00:00:00+01:00'));
    }

    public function testCreateExpense()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

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
    }

    public function testDeleteExpense()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->delete_expense(201);
    }

    /* Subject */

    public function testGetSubjects()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subjects = $f->get_subjects();
    }

    public function testGetSubject()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subject = $f->get_subject(36);
    }

    public function testCreateSubject()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subject = $f->create_subject(array('name' => 'Apple Czech s.r.o.'));
    }

    public function testUpdateSubject()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subject = $f->update_subject(36, array('street' => 'Tetst'));
    }

    public function testDeleteSubject()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->delete_subject(36);
    }

    public function testSearchSubjects()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $subjects = $f->search_subjects(array('query' => 'Apple'));
    }

    /* Generator */

    public function testGetGenerators()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generators = $f->get_generators();
    }

    public function testGetTemplateGenerators()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generators = $f->get_template_generators();
    }

    public function testGetRecurringGenerators()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generators = $f->get_recurring_generators();
    }

    public function testGetGenerator()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generator = $f->get_generator(10);
    }

    public function testCreateGenerator()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

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
    }

    public function testUpdateGenerator()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $generator = $f->update_generator(10, array('due' => 5));
    }

    public function testDeleteGenerator()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $response = $f->delete_generator(10);
    }

    /* Message */

    public function testCreateMessage()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $message = $f->create_message(86, array(
            'email' => 'test@example.org',
            'subject' => 'Hello',
            'message' => "Hello,\n\nI have invoice for you.\n#link#\n\n   John Doe"
        ));
    }

    /* Event */

    public function testGetEvents()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $events = $f->get_events();
    }

    public function testGetPaidEvents()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $events = $f->get_paid_events();
    }

    /* Todo */

    public function testGetTodos()
    {
        $requester = $this->createMock('FakturoidRequester');
        $requester->method('run')->willReturn(null);

        $f = new Fakturoid('test', 'test@example.org', 'api-key', 'Test <test@example.org>', array('requester' => $requester));
        $todos = $f->get_todos();
    }
}
