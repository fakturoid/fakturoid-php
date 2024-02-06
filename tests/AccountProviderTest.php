<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\AccountProvider;
use Fakturoid\Response;

class AccountProviderTest extends \Fakturoid\Tests\TestCase
{
    public function testGetAccount(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(
            200,
            'application/json',
            '{"subdomain": "fakturoid-account-slug"}'
        );

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/account.json')
            ->willReturn(new Response($responseInterface));

        $provider = new AccountProvider($dispatcher);
        $response = $provider->get();
        $data = (object) ['subdomain' => 'fakturoid-account-slug'];
        $this->assertEquals($data, $response->getBody());
        $this->assertEquals((array) $data, $response->getBody(true));
    }
}
