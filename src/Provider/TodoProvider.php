<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class TodoProvider extends Provider
{
    public function __construct(
        private readonly DispatcherInterface $dispatcher
    ) {
    }

    /**
     * @param array{'since'?:string, 'page'?:int} $params
     */
    public function list(array $params = []): Response
    {
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/todos.json',
            $this->filterOptions($params, ['since', 'page'])
        );
    }

    public function getToggleCompletion(int $id): Response
    {
        return $this->dispatcher->get(
            sprintf(
                '/accounts/{accountSlug}/todos/%d/toggle_completion.json',
                $id
            )
        );
    }
}
