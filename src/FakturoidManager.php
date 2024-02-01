<?php

namespace Fakturoid;

use Fakturoid\Auth\AuthProvider;
use Fakturoid\Auth\CredentialCallback;
use Fakturoid\Auth\Credentials;
use Fakturoid\Enum\AuthTypeEnum;
use Fakturoid\Exception\AuthorizationFailedException;
use Fakturoid\Provider\AccountProvider;
use Fakturoid\Provider\BankAccountProvider;
use Fakturoid\Provider\EventProvider;
use Fakturoid\Provider\ExpenseProvider;
use Fakturoid\Provider\GeneratorProvider;
use Fakturoid\Provider\InboxFileProvider;
use Fakturoid\Provider\InventoryItemProvider;
use Fakturoid\Provider\InventoryMoveProvider;
use Fakturoid\Provider\InvoiceProvider;
use Fakturoid\Provider\NumberFormatProvider;
use Fakturoid\Provider\RecurringGeneratorProvider;
use Fakturoid\Provider\SubjectProvider;
use Fakturoid\Provider\TodoProvider;
use Fakturoid\Provider\UserProvider;
use Psr\Http\Client\ClientInterface;

class FakturoidManager
{
    private readonly AuthProvider $authProvider;
    private readonly Dispatcher $dispatcher;
    private readonly AccountProvider $accountProvider;
    private readonly BankAccountProvider $bankAccountProvider;
    private readonly EventProvider $eventProvider;
    private readonly ExpenseProvider $expenseProvider;
    private readonly GeneratorProvider $generatorProvider;
    private readonly InboxFileProvider $inboxFileProvider;
    private readonly InventoryItemProvider $inventoryItemsProvider;
    private readonly InventoryMoveProvider $inventoryMoveProvider;
    private readonly InvoiceProvider $invoiceProvider;
    private readonly NumberFormatProvider $numberFormatProvider;
    private readonly RecurringGeneratorProvider $recurringGeneratorProvider;
    private readonly SubjectProvider $subjectProvider;
    private readonly TodoProvider $todoProvider;
    private readonly UserProvider $userProvider;

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

        $this->accountProvider = new AccountProvider($this->dispatcher);
        $this->bankAccountProvider = new BankAccountProvider($this->dispatcher);
        $this->eventProvider = new EventProvider($this->dispatcher);
        $this->expenseProvider = new ExpenseProvider($this->dispatcher);
        $this->generatorProvider = new GeneratorProvider($this->dispatcher);
        $this->inboxFileProvider = new InboxFileProvider($this->dispatcher);
        $this->inventoryItemsProvider = new InventoryItemProvider($this->dispatcher);
        $this->inventoryMoveProvider = new InventoryMoveProvider($this->dispatcher);
        $this->invoiceProvider = new InvoiceProvider($this->dispatcher);
        $this->numberFormatProvider = new NumberFormatProvider($this->dispatcher);
        $this->recurringGeneratorProvider = new RecurringGeneratorProvider($this->dispatcher);
        $this->subjectProvider = new SubjectProvider($this->dispatcher);
        $this->todoProvider = new TodoProvider($this->dispatcher);
        $this->userProvider = new UserProvider($this->dispatcher);
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

    public function getDispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }

    public function getAccountProvider(): AccountProvider
    {
        return $this->accountProvider;
    }

    public function getBankAccountProvider(): BankAccountProvider
    {
        return $this->bankAccountProvider;
    }

    public function getEventProvider(): EventProvider
    {
        return $this->eventProvider;
    }

    public function getExpenseProvider(): ExpenseProvider
    {
        return $this->expenseProvider;
    }

    public function getGeneratorProvider(): GeneratorProvider
    {
        return $this->generatorProvider;
    }

    public function getInboxFileProvider(): InboxFileProvider
    {
        return $this->inboxFileProvider;
    }

    public function getInventoryItemsProvider(): InventoryItemProvider
    {
        return $this->inventoryItemsProvider;
    }

    public function getInventoryMoveProvider(): InventoryMoveProvider
    {
        return $this->inventoryMoveProvider;
    }

    public function getInvoiceProvider(): InvoiceProvider
    {
        return $this->invoiceProvider;
    }

    public function getNumberFormatProvider(): NumberFormatProvider
    {
        return $this->numberFormatProvider;
    }

    public function getRecurringGeneratorProvider(): RecurringGeneratorProvider
    {
        return $this->recurringGeneratorProvider;
    }

    public function getSubjectProvider(): SubjectProvider
    {
        return $this->subjectProvider;
    }

    public function getTodoProvider(): TodoProvider
    {
        return $this->todoProvider;
    }

    public function getUserProvider(): UserProvider
    {
        return $this->userProvider;
    }
}
