<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class AccountProvider extends Provider
{
    public function __construct(
        private readonly DispatcherInterface $dispatcher
    ) {
    }

    public function get(): Response
    {
        return $this->dispatcher->get('/accounts/{accountSlug}/account.json');
    }
}
