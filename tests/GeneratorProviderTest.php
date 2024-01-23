<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\GeneratorProvider;
use Fakturoid\Response;

class GeneratorProviderTest extends TestCase
{
    public function testList(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/generators.json', ['page' => 1])
            ->willReturn(new Response($responseInterface));

        $provider = new GeneratorProvider($dispatcher);
        $response = $provider->list(['page' => 1]);
        $this->assertEquals([], $response->getBody(true));
    }

    public function testGet(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with(sprintf('/accounts/{accountSlug}/generators/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new GeneratorProvider($dispatcher);
        $response = $provider->get($id);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testDelete(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('delete')
            ->with(sprintf('/accounts/{accountSlug}/generators/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new GeneratorProvider($dispatcher);
        $response = $provider->delete($id);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testUpdate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('patch')
            ->with(sprintf('/accounts/{accountSlug}/generators/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new GeneratorProvider($dispatcher);
        $response = $provider->update($id, ['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testCreate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with('/accounts/{accountSlug}/generators.json')
            ->willReturn(new Response($responseInterface));

        $provider = new GeneratorProvider($dispatcher);
        $response = $provider->create(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testListRecurring(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/recurring_generators.json', ['page' => 1])
            ->willReturn(new Response($responseInterface));

        $provider = new GeneratorProvider($dispatcher);
        $response = $provider->listRecurring(['page' => 1]);
        $this->assertEquals([], $response->getBody(true));
    }

    public function testGetRecurring(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with(sprintf('/accounts/{accountSlug}/recurring_generators/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new GeneratorProvider($dispatcher);
        $response = $provider->getRecurring($id);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testDeleteRecurring(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('delete')
            ->with(sprintf('/accounts/{accountSlug}/recurring_generators/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new GeneratorProvider($dispatcher);
        $response = $provider->deleteRecurring($id);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testUpdateRecurring(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('patch')
            ->with(sprintf('/accounts/{accountSlug}/recurring_generators/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new GeneratorProvider($dispatcher);
        $response = $provider->updateRecurring($id, ['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testCreateRecurring(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with('/accounts/{accountSlug}/recurring_generators.json')
            ->willReturn(new Response($responseInterface));

        $provider = new GeneratorProvider($dispatcher);
        $response = $provider->createRecurring(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }
}
