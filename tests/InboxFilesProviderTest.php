<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\InboxFilesProvider;
use Fakturoid\Response;

class InboxFilesProviderTest extends TestCase
{
    public function testList(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/inbox_files.json')
            ->willReturn(new Response($responseInterface));

        $provider = new InboxFilesProvider($dispatcher);
        $response = $provider->list();
        $this->assertEquals([], $response->getBody(true));
    }

    public function testCreate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with('/accounts/{accountSlug}/inbox_files.json')
            ->willReturn(new Response($responseInterface));

        $provider = new InboxFilesProvider($dispatcher);
        $response = $provider->create(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testDelete(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('delete')
            ->with(sprintf('/accounts/{accountSlug}/inbox_files/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new InboxFilesProvider($dispatcher);
        $response = $provider->delete($id);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testSendToOCR(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with(sprintf('/accounts/{accountSlug}/inbox_files/%d/send_to_ocr.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new InboxFilesProvider($dispatcher);
        $response = $provider->sendToOCR($id);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testDownload(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/pdf', 'binary file');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with(sprintf('/accounts/{accountSlug}/inbox_files/%d/download', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new InboxFilesProvider($dispatcher);
        $response = $provider->download($id);
        $this->assertEquals('binary file', $response->getBody(true));
    }
}
