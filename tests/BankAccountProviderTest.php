<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\BankAccountProvider;
use Fakturoid\Response;

class BankAccountProviderTest extends \Fakturoid\Tests\TestCase
{
    public function testListBankAccount(): void
    {
        $data = [
            (object) [
                'id' => 169116,
                'name' => 'Pokladna',
                'currency' => 'CZK',
                'number' => null,
                'iban' => null,
                'swift_bic' => null,
                'pairing' => false,
                'expense_pairing' => false,
                'payment_adjustment' => false,
                'default' => false,
                'created_at' => '2020-12-16T09:16:29.741+01:00',
                'updated_at' => '2020-12-16T09:16:29.741+01:00'
            ]
        ];
        $returnedData = json_encode($data);
        $this->assertNotFalse($returnedData);

        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(
            200,
            'application/json',
            $returnedData
        );

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/bank_accounts.json')
            ->willReturn(new Response($responseInterface));

        $provider = new BankAccountProvider($dispatcher);
        $response = $provider->list();
        $this->assertEquals($data, $response->getBody());
        $this->assertEquals([(array) $data[0]], $response->getBody(true));
    }
}
