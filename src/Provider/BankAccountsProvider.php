<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class BankAccountsProvider extends Provider
{
    public function __construct(
        private readonly DispatcherInterface $dispatcher
    ) {
    }

    public function list(): Response
    {
        return $this->dispatcher->get('/accounts/{accountSlug}/bank_accounts.json');
    }
}
