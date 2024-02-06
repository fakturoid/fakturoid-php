<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\NumberFormatsProvider;
use Fakturoid\Response;

class NumberFormatsProviderTest extends \Fakturoid\Tests\TestCase
{
    public function testListNumberFormat(): void
    {
        $data = [
            (object) [
                'id' => 237041,
                'format' => 'F#yyyy##ddddd#',
                'preview' => 'F202400001, F202400002, ..., F202499999',
                'default' => true,
                'created_at' => '2021-01-12T15:46:03.371+01:00',
                'updated_at' => '2022-01-06T21:09:49.550+01:00'
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
            ->with('/accounts/{accountSlug}/number_formats/invoices.json')
            ->willReturn(new Response($responseInterface));

        $provider = new NumberFormatsProvider($dispatcher);
        $response = $provider->list();
        $this->assertEquals($data, $response->getBody());
        $this->assertEquals([(array) $data[0]], $response->getBody(true));
    }
}
