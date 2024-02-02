<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\TodosProvider;
use Fakturoid\Response;

class TodosProviderTest extends TestCase
{
    public function testList(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/todos.json', [])
            ->willReturn(new Response($responseInterface));

        $provider = new TodosProvider($dispatcher);
        $response = $provider->list();
        $this->assertEquals([], $response->getBody(true));

        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/todos.json', ['page' => 2])
            ->willReturn(new Response($responseInterface));

        $provider = new TodosProvider($dispatcher);
        $response = $provider->list(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testToggleCompletion(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');
        $id = 6;
        $dispatcher->expects($this->once())
            ->method('post')
            ->with(sprintf('/accounts/{accountSlug}/todos/%d/toggle_completion.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new TodosProvider($dispatcher);
        $response = $provider->toggleCompletion($id);
        $this->assertEquals([], $response->getBody(true));
    }
}
