<?php

declare(strict_types=1);

namespace Fakturoid\Tests\Integration;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class TodosProviderIntegrationTest extends IntegrationTestCase
{
    public function testListTodos(): void
    {
        $response = $this->getManager()->getTodosProvider()->list();

        self::assertEquals(200, $response->getStatusCode());

        $todos = $response->getBody(true);
        self::assertIsArray($todos);
        self::assertNotEmpty($todos);
        self::assertArrayHasKey(0, $todos);
        self::assertIsArray($todos[0]);
        self::assertArrayHasKey('id', $todos[0]);
        self::assertArrayHasKey('name', $todos[0]);
        self::assertArrayHasKey('completed_at', $todos[0]);
    }

    public function testListTodosWithPagination(): void
    {
        $response = $this->getManager()->getTodosProvider()->list(['page' => 1]);

        self::assertEquals(200, $response->getStatusCode());

        $todos = $response->getBody(true);
        self::assertIsArray($todos);
    }

    public function testToggleTodoCompletion(): void
    {
        $listResponse = $this->getManager()->getTodosProvider()->list();
        $todos = $listResponse->getBody(true);
        self::assertIsArray($todos);
        self::assertNotEmpty($todos);
        self::assertArrayHasKey(0, $todos);
        self::assertIsArray($todos[0]);
        self::assertArrayHasKey('id', $todos[0]);
        self::assertArrayHasKey('completed_at', $todos[0]);

        $todoId = $todos[0]['id'];
        self::assertIsInt($todoId);
        $originalStatus = $todos[0]['completed_at'];

        // Toggle completion
        $response = $this->getManager()->getTodosProvider()->toggleCompletion($todoId);
        self::assertEquals(200, $response->getStatusCode());

        $todo = $response->getBody();
        self::assertIsObject($todo);
        self::assertObjectHasProperty('id', $todo);
        self::assertObjectHasProperty('completed_at', $todo);

        self::assertNotEquals($originalStatus, $todo->completed_at);

        $this->getManager()->getTodosProvider()->toggleCompletion($todoId);
    }
}
