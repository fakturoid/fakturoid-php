<?php

namespace Fakturoid;

use Fakturoid\Auth\AuthProvider;
use Fakturoid\Auth\CredentialCallback;
use Fakturoid\Auth\Credentials;
use Fakturoid\Enum\AuthTypeEnum;
use Fakturoid\Exception\AuthorizationFailedException;
use Fakturoid\Provider\EventProvider;
use Fakturoid\Provider\ExpenseProvider;
use Fakturoid\Provider\GeneratorProvider;
use Fakturoid\Provider\InboxFileProvider;
use Fakturoid\Provider\InventoryItemProvider;
use Fakturoid\Provider\InvoiceProvider;
use Fakturoid\Provider\SettingProvider;
use Fakturoid\Provider\SubjectProvider;
use Fakturoid\Provider\TodoProvider;
use Psr\Http\Client\ClientInterface;

class FakturoidManager
{
    private readonly AuthProvider $authProvider;
    private readonly Dispatcher $dispatcher;
    private readonly InvoiceProvider $invoiceProvider;
    private readonly SubjectProvider $subjectProvider;
    private readonly ExpenseProvider $expensesProvider;
    private readonly InventoryItemProvider $inventoryItemsProvider;
    private readonly InboxFileProvider $inboxFileProvider;
    private readonly GeneratorProvider $generatorProvider;
    private readonly SettingProvider $settingProvider;
    private readonly EventProvider $eventProvider;
    private readonly TodoProvider $todoProvider;

    public function __construct(
        ClientInterface $client,
        string $clientId,
        string $clientSecret,
        string $userAgent,
        ?string $accountSlug = null,
        ?string $redirectUri = null
    ) {
        $this->authProvider = new AuthProvider($clientId, $clientSecret, $redirectUri, $client);

        $this->dispatcher = new Dispatcher($userAgent, $this->authProvider, $client, $accountSlug);
        $this->invoiceProvider = new InvoiceProvider($this->dispatcher);
        $this->subjectProvider = new SubjectProvider($this->dispatcher);
        $this->expensesProvider = new ExpenseProvider($this->dispatcher);
        $this->inventoryItemsProvider = new InventoryItemProvider($this->dispatcher);
        $this->inboxFileProvider = new InboxFileProvider($this->dispatcher);
        $this->generatorProvider = new GeneratorProvider($this->dispatcher);
        $this->settingProvider = new SettingProvider($this->dispatcher);
        $this->eventProvider = new EventProvider($this->dispatcher);
        $this->todoProvider = new TodoProvider($this->dispatcher);
    }

    public function setAccountSlug(string $companySlug): void
    {
        $this->dispatcher->setAccountSlug($companySlug);
    }

    public function getAuthProvider(): AuthProvider
    {
        return $this->authProvider;
    }

    public function getAuthenticationUrl(): string
    {
        return $this->authProvider->getAuthenticationUrl();
    }

    /**
     * @throws AuthorizationFailedException
     */
    public function requestCredentials(string $code): void
    {
        $this->authProvider->loadCode($code);
        $this->authProvider->auth(AuthTypeEnum::AUTHORIZATION_CODE_FLOW);
    }

    public function getCredentials(): ?Credentials
    {
        return $this->authProvider->getCredentials();
    }

    public function setCredentials(Credentials $credentials): void
    {
        $this->authProvider->setCredentials($credentials);
    }

    public function setCredentialsCallback(CredentialCallback $callback): void
    {
        $this->authProvider->setCredentialsCallback($callback);
    }

    /**
     * @throws AuthorizationFailedException
     */
    public function authClientCredentials(): void
    {
        $this->authProvider->auth(AuthTypeEnum::CLIENT_CREDENTIALS_CODE_FLOW);
    }

    public function getInvoiceProvider(): InvoiceProvider
    {
        return $this->invoiceProvider;
    }

    public function getSubjectProvider(): SubjectProvider
    {
        return $this->subjectProvider;
    }

    public function getExpensesProvider(): ExpenseProvider
    {
        return $this->expensesProvider;
    }

    public function getInventoryItemsProvider(): InventoryItemProvider
    {
        return $this->inventoryItemsProvider;
    }

    public function getInboxFileProvider(): InboxFileProvider
    {
        return $this->inboxFileProvider;
    }

    public function getGeneratorProvider(): GeneratorProvider
    {
        return $this->generatorProvider;
    }

    public function getSettingProvider(): SettingProvider
    {
        return $this->settingProvider;
    }

    public function getEventProvider(): EventProvider
    {
        return $this->eventProvider;
    }

    public function getTodoProvider(): TodoProvider
    {
        return $this->todoProvider;
    }
}
