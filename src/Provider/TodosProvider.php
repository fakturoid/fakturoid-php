<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class TodosProvider extends Provider
{
    /**
     * @readonly
     */
    private DispatcherInterface $dispatcher;

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
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

    public function toggleCompletion(int $id): Response
    {
        return $this->dispatcher->post(
            sprintf(
                '/accounts/{accountSlug}/todos/%d/toggle_completion.json',
                $id
            )
        );
    }
}
