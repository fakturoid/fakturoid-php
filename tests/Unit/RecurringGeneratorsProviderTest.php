<?php

namespace Fakturoid\Tests\Unit;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\RecurringGeneratorsProvider;
use Fakturoid\Response;

class RecurringGeneratorsProviderTest extends UnitTestCase
{
    public function testList(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/recurring_generators.json', ['page' => 1])
            ->willReturn(new Response($responseInterface));

        $provider = new RecurringGeneratorsProvider($dispatcher);
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

        $provider = new RecurringGeneratorsProvider($dispatcher);
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

        $provider = new RecurringGeneratorsProvider($dispatcher);
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

        $provider = new RecurringGeneratorsProvider($dispatcher);
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

        $provider = new RecurringGeneratorsProvider($dispatcher);
        $response = $provider->create(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testPause(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"active": false}');
        $dispatcher->expects($this->once())
            ->method('patch')
            ->with(sprintf('/accounts/{accountSlug}/recurring_generators/%d/pause.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new RecurringGeneratorsProvider($dispatcher);
        $response = $provider->pause($id);
        $this->assertEquals(['active' => false], $response->getBody(true));
    }

    public function testActivate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"active": true}');
        $dispatcher->expects($this->once())
            ->method('patch')
            ->with(sprintf('/accounts/{accountSlug}/recurring_generators/%d/activate.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new RecurringGeneratorsProvider($dispatcher);
        $response = $provider->activate($id);
        $this->assertEquals(['active' => true], $response->getBody(true));
    }

    public function testActivateWithNextOccurrenceDate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(
            200,
            'application/json',
            '{"active": true, "next_occurrence_on": "2025-02-15"}'
        );
        $dispatcher->expects($this->once())
            ->method('patch')
            ->with(
                sprintf('/accounts/{accountSlug}/recurring_generators/%d/activate.json', $id),
                ['next_occurrence_on' => '2025-02-15']
            )
            ->willReturn(new Response($responseInterface));

        $provider = new RecurringGeneratorsProvider($dispatcher);
        $response = $provider->activate($id, ['next_occurrence_on' => '2025-02-15']);
        $this->assertEquals(
            ['active' => true, 'next_occurrence_on' => '2025-02-15'],
            $response->getBody(true)
        );
    }
}
