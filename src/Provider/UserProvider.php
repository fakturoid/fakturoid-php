<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class UserProvider extends Provider
{
    public function __construct(
        private readonly DispatcherInterface $dispatcher
    ) {
    }

    public function getCurrentUser(): Response
    {
        return $this->dispatcher->get('/user.json');
    }

    public function list(): Response
    {
        return $this->dispatcher->get('/accounts/{accountSlug}/users.json');
    }
}
