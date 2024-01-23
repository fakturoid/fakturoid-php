<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\InventoryItemProvider;
use Fakturoid\Response;

class InventoryItemProviderTest extends TestCase
{
    public function testList(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/inventory_items.json', ['page' => 1])
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->list(['page' => 1]);
        $this->assertEquals([], $response->getBody(true));
    }

    public function testListArchived(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/inventory_items/archived.json', ['page' => 1])
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->listArchived(['page' => 1]);
        $this->assertEquals([], $response->getBody(true));
    }

    public function testListLowQuantity(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/inventory_items/low_quantity.json', ['page' => 1])
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->listLowQuantity(['page' => 1]);
        $this->assertEquals([], $response->getBody(true));
    }

    public function testSearch(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/inventory_items/search.json', ['page' => 2])
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->search(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testGet(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
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
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
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
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->update($id, ['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testCreate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with('/accounts/{accountSlug}/inventory_items.json')
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->create(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testArchive(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d/archive.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->archive($id);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testUnArchive(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d/unarchive.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->unArchive($id);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testListMove(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/inventory_moves.json', ['page' => 1])
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->listMoves(['page' => 1]);
        $this->assertEquals([], $response->getBody(true));
    }

    public function testGetMove(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $itemId = 8;
        $moveId = 60;
        $dispatcher->expects($this->once())
            ->method('get')
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d/inventory_moves/%d.json', $itemId, $moveId))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->getMove($itemId, $moveId);
        $this->assertEquals([], $response->getBody(true));
    }

    public function testDeleteMove(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $itemId = 8;
        $moveId = 60;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('delete')
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d/inventory_moves/%d.json', $itemId, $moveId))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->deleteMove($itemId, $moveId);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testUpdateMove(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $itemId = 8;
        $moveId = 60;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('patch')
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d/inventory_moves/%d.json', $itemId, $moveId))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->updateMove($itemId, $moveId, ['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testCreateMove(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $itemId = 8;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with(sprintf('/accounts/{accountSlug}/inventory_items/%d/inventory_moves.json', $itemId))
            ->willReturn(new Response($responseInterface));

        $provider = new InventoryItemProvider($dispatcher);
        $response = $provider->createMove($itemId, ['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }
}
