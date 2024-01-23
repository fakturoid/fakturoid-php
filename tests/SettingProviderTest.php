<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\SettingProvider;
use Fakturoid\Response;

class SettingProviderTest extends TestCase
{
    public function testGetCurrentUser(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"id": 2, "name": "John Doe"}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/user.json')
            ->willReturn(new Response($responseInterface));

        $provider = new SettingProvider($dispatcher);
        $response = $provider->getCurrentUser();
        $this->assertEquals(['id' => 2, 'name' => 'John Doe'], $response->getBody(true));
    }

    public function testListUsers(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '[{"id": 2, "name": "John Doe"}]');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/users.json')
            ->willReturn(new Response($responseInterface));

        $provider = new SettingProvider($dispatcher);
        $response = $provider->listUsers();
        $this->assertEquals([['id' => 2, 'name' => 'John Doe']], $response->getBody(true));
    }

    public function testAccount(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '[{"id": 2, "name": "John Doe"}]');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/account.json')
            ->willReturn(new Response($responseInterface));

        $provider = new SettingProvider($dispatcher);
        $response = $provider->getAccount();
        $this->assertEquals([['id' => 2, 'name' => 'John Doe']], $response->getBody(true));
    }

    public function testListBankAccounts(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '[{"id": 2, "name": "John Doe"}]');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/bank_accounts.json')
            ->willReturn(new Response($responseInterface));

        $provider = new SettingProvider($dispatcher);
        $response = $provider->listBankAccounts();
        $this->assertEquals([['id' => 2, 'name' => 'John Doe']], $response->getBody(true));
    }

    public function testListNumberFormats(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '[{"id": 2, "name": "John Doe"}]');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/number_formats/invoices.json')
            ->willReturn(new Response($responseInterface));

        $provider = new SettingProvider($dispatcher);
        $response = $provider->listInvoiceNumberFormats();
        $this->assertEquals([['id' => 2, 'name' => 'John Doe']], $response->getBody(true));
    }
}
