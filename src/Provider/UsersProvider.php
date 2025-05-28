<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class UsersProvider extends Provider
{
    /**
     * @readonly
     */
    private DispatcherInterface $dispatcher;

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
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
