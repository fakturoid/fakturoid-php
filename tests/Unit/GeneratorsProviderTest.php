<?php

namespace Fakturoid\Tests\Unit;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\GeneratorsProvider;
use Fakturoid\Response;

class GeneratorsProviderTest extends UnitTestCase
{
    public function testList(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/generators.json', ['page' => 1])
            ->willReturn(new Response($responseInterface));

        $provider = new GeneratorsProvider($dispatcher);
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

        $provider = new GeneratorsProvider($dispatcher);
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

        $provider = new GeneratorsProvider($dispatcher);
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

        $provider = new GeneratorsProvider($dispatcher);
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

        $provider = new GeneratorsProvider($dispatcher);
        $response = $provider->create(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }
}
