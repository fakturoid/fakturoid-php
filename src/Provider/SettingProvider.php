<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class SettingProvider extends Provider
{
    public function __construct(
        private readonly DispatcherInterface $dispatcher
    ) {
    }

    public function getCurrentUser(): Response
    {
        return $this->dispatcher->get('/user.json');
    }

    public function listUsers(): Response
    {
        return $this->dispatcher->get('/accounts/{accountSlug}/users.json');
    }

    public function getAccount(): Response
    {
        return $this->dispatcher->get('/accounts/{accountSlug}/account.json');
    }

    public function listBankAccounts(): Response
    {
        return $this->dispatcher->get('/accounts/{accountSlug}/bank_accounts.json');
    }

    public function listInvoiceNumberFormats(): Response
    {
        return $this->dispatcher->get('/accounts/{accountSlug}/number_formats/invoices.json');
    }
}
