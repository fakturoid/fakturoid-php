<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\RecurringGeneratorProvider;
use Fakturoid\Response;

class RecurringGeneratorProviderTest extends \Fakturoid\Tests\TestCase
{
    public function testList(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/recurring_generators.json', ['page' => 1])
            ->willReturn(new Response($responseInterface));

        $provider = new RecurringGeneratorProvider($dispatcher);
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
            ->with(sprintf('/accounts/{accountSlug}/recurring_generators/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new RecurringGeneratorProvider($dispatcher);
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
            ->with(sprintf('/accounts/{accountSlug}/recurring_generators/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new RecurringGeneratorProvider($dispatcher);
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
            ->with(sprintf('/accounts/{accountSlug}/recurring_generators/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new RecurringGeneratorProvider($dispatcher);
        $response = $provider->update($id, ['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testCreate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with('/accounts/{accountSlug}/recurring_generators.json')
            ->willReturn(new Response($responseInterface));

        $provider = new RecurringGeneratorProvider($dispatcher);
        $response = $provider->create(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }
}
