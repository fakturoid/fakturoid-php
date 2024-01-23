<?php

namespace Fakturoid\Tests;

use Fakturoid\Dispatcher;
use Fakturoid\Provider\ExpenseProvider;
use Fakturoid\Response;

class ExpenseProviderTest extends TestCase
{
    public function testList(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{}');

        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/expenses.json', ['page' => 1])
            ->willReturn(new Response($responseInterface));

        $provider = new ExpenseProvider($dispatcher);
        $response = $provider->list(['page' => 1]);
        $this->assertEquals([], $response->getBody(true));
    }

    public function testSearch(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with('/accounts/{accountSlug}/expenses/search.json', ['page' => 2])
            ->willReturn(new Response($responseInterface));

        $provider = new ExpenseProvider($dispatcher);
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
            ->with(sprintf('/accounts/{accountSlug}/expenses/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new ExpenseProvider($dispatcher);
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
            ->with(sprintf('/accounts/{accountSlug}/expenses/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new ExpenseProvider($dispatcher);
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
            ->with(sprintf('/accounts/{accountSlug}/expenses/%d.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new ExpenseProvider($dispatcher);
        $response = $provider->update($id, ['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testCreate(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with('/accounts/{accountSlug}/expenses.json')
            ->willReturn(new Response($responseInterface));

        $provider = new ExpenseProvider($dispatcher);
        $response = $provider->create(['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testCreatePayment(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with(sprintf('/accounts/{accountSlug}/expenses/%d/payments.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new ExpenseProvider($dispatcher);
        $response = $provider->createPayment($id, ['page' => 2]);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testDeletePayment(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $paymentId = 8;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('delete')
            ->with(sprintf('/accounts/{accountSlug}/expenses/%d/payments/%d.json', $id, $paymentId))
            ->willReturn(new Response($responseInterface));

        $provider = new ExpenseProvider($dispatcher);
        $response = $provider->deletePayment($id, $paymentId);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testGetAttachment(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $paymentId = 8;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('get')
            ->with(sprintf('/accounts/{accountSlug}/expenses/%d/attachments/%d/download', $id, $paymentId))
            ->willReturn(new Response($responseInterface));

        $provider = new ExpenseProvider($dispatcher);
        $response = $provider->getAttachment($id, $paymentId);
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }

    public function testFireAction(): void
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $id = 6;
        $responseInterface = $this->createPsrResponseMock(200, 'application/json', '{"page": 2}');
        $dispatcher->expects($this->once())
            ->method('post')
            ->with(sprintf('/accounts/{accountSlug}/expenses/%d/fire.json', $id))
            ->willReturn(new Response($responseInterface));

        $provider = new ExpenseProvider($dispatcher);
        $response = $provider->fireAction($id, 'pay');
        $this->assertEquals(['page' => 2], $response->getBody(true));
    }
}
