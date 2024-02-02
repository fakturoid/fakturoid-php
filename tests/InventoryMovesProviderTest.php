<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\InventoryMovesProvider;
use Fakturoid\Response;

class InventoryMovesProviderTest extends \Fakturoid\Tests\TestCase
{
    public function testList(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/inventory_moves.json', ['page' => 1])
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryMovesProvider($dispatcher);
        $response = $provider->list(['page' => 1]);
        $this->assertEquals([], $response->getBody(true));
    }

    public function testGet(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $itemId = 8;
        $moveId = 60;
        $dispatcher->expects($this->once())
            ->method('get')
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d/inventory_moves/%d.json', $itemId, $moveId))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryMovesProvider($dispatcher);
        $response = $provider->get($itemId, $moveId);
        $this->assertEquals([], $response->getBody(true));
    }

    public function testDelete(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $itemId = 8;
        $moveId = 60;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('delete')
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d/inventory_moves/%d.json', $itemId, $moveId))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryMovesProvider($dispatcher);
        $response = $provider->delete($itemId, $moveId);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testUpdate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $itemId = 8;
        $moveId = 60;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('patch')
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d/inventory_moves/%d.json', $itemId, $moveId))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryMovesProvider($dispatcher);
        $response = $provider->update($itemId, $moveId, ['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testCreate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $itemId = 8;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d/inventory_moves.json', $itemId))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryMovesProvider($dispatcher);
        $response = $provider->create($itemId, ['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }
}
