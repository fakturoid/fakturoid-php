<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\TodoProvider;
use Fakturoid\Response;

class TodoProviderTest extends TestCase
{
    public function testList(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/todos.json', [])
            ->willReturn(new Response($responseInterface));

        $provider = new TodoProvider($dispatcher);
        $response = $provider->list();
        $this->assertEquals([], $response->getBody(true));

        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/todos.json', ['page' => 2])
            ->willReturn(new Response($responseInterface));

        $provider = new TodoProvider($dispatcher);
        $response = $provider->list(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testGetToggleCompletion(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');
        $id = 6;
        $dispatcher->expects($this->once())
            ->method('get')
            ->with(sprintf('/accounts/{accountSlug}/todos/%d/toggle_completion.json', $id), [])
            ->willReturn(new Response($responseInterface));

        $provider = new TodoProvider($dispatcher);
        $response = $provider->getToggleCompletion($id);
        $this->assertEquals([], $response->getBody(true));
    }
}
