<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\UserProvider;
use Fakturoid\Response;

class UserProviderTest extends \Fakturoid\Tests\TestCase
{
    public function testGetCurrentUser(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(
            200,
            'application/json',
            '{"id": 1, "full_name": "Fakturoid"}'
        );

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/user.json')
            ->willReturn(new Response($responseInterface));
        $data = (object) [
            'id' => 1,
            'full_name' => 'Fakturoid'
        ];

        $provider = new UserProvider($dispatcher);
        $response = $provider->getCurrentUser();
        $this->assertEquals($data, $response->getBody());
        $this->assertEquals((array) $data, $response->getBody(true));
    }

    public function testListUser(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(
            200,
            'application/json',
            '[{"id": 1, "full_name": "Fakturoid"}]'
        );

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/users.json')
            ->willReturn(new Response($responseInterface));

        $data = [
            (object) [
                'id' => 1,
                'full_name' => 'Fakturoid'
            ]
        ];
        $provider = new UserProvider($dispatcher);
        $response = $provider->list();
        $this->assertEquals($data, $response->getBody());
        $this->assertEquals([(array) $data[0]], $response->getBody(true));
    }
}
