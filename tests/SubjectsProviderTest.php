<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\SubjectsProvider;
use Fakturoid\Response;

class SubjectsProviderTest extends TestCase
{
    public function testList(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/subjects.json', [])
            ->willReturn(
                new Response($responseInterface)
            );

        $provider = new SubjectsProvider($dispatcher);
        $response = $provider->list();
        $this->assertEquals([], $response->getBody(true));

        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/subjects.json', ['page' => 2])
            ->willReturn(
                new Response($responseInterface)
            );

        $provider = new SubjectsProvider($dispatcher);
        $response = $provider->list(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testSearch(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $querySearch = ['query' => 'test@fakturoid.cz', 'page' => 2];

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"data": "test@fakturoid.cz"}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/subjects/search.json', $querySearch)
            ->willReturn(
                new Response($responseInterface)
            );

        $provider = new SubjectsProvider($dispatcher);
        $response = $provider->search($querySearch);

        $this->assertEquals(['data' => 'test@fakturoid.cz'], $response->getBody(true));
    }

    public function testGet(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"data": "test@fakturoid.cz"}');
        $id = 6;
        $dispatcher->expects($this->once())
            ->method('get')
            ->with(sprintf('/accounts/{accountSlug}/subjects/%d.json', $id))
            ->willReturn(
                new Response($responseInterface)
            );

        $provider = new SubjectsProvider($dispatcher);
        $response = $provider->get($id);

        $this->assertEquals(['data' => 'test@fakturoid.cz'], $response->getBody(true));
    }

    public function testCreate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $subjectData = ['name' => 'test'];
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"name": "test"}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with('/accounts/{accountSlug}/subjects.json', $subjectData)
            ->willReturn(
                new Response($responseInterface)
            );

        $provider = new SubjectsProvider($dispatcher);
        $response = $provider->create($subjectData);

        $this->assertEquals($subjectData, $response->getBody(true));
    }

    public function testSearchWithUnsupportedQuery(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $querySearch = ['query' => 'test@fakturoid.cz', 'page' => 2];
        $provider = new SubjectsProvider($dispatcher);

        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, E_ALL);

        try {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('Unknown option keys: unknown');
            $response = $provider->search($querySearch + ['unknown' => 'unknown']);

            $this->assertEquals(['data' => 'test@fakturoid.cz'], $response->getBody(true));
        } finally {
            restore_error_handler(); // Odstranění chybového handleru
        }
    }

    public function testUpdate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $subjectData = ['name' => 'test'];
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"name": "test"}');
        $dispatcher->expects($this->once())
            ->method('patch')
            ->with(sprintf('/accounts/{accountSlug}/subjects/%d.json', $id), $subjectData)
            ->willReturn(
                new Response($responseInterface)
            );

        $provider = new SubjectsProvider($dispatcher);
        $response = $provider->update($id, $subjectData);

        $this->assertEquals($subjectData, $response->getBody(true));
    }

    public function testDelete(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(
            200,
            'application/json',
            '{"data": "test@fakturoid.cz"}'
        );
        $id = 6;
        $dispatcher->expects($this->once())
            ->method('delete')
            ->with(sprintf('/accounts/{accountSlug}/subjects/%d.json', $id))
            ->willReturn(
                new Response($responseInterface)
            );

        $provider = new SubjectsProvider($dispatcher);
        $response = $provider->delete($id);

        $this->assertEquals(['data' => 'test@fakturoid.cz'], $response->getBody(true));
    }
}
