<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class SubjectProvider extends Provider
{
    public function __construct(
        private readonly DispatcherInterface $dispatcher
    ) {
    }

    /**
     * @param array{'since'?:string, 'updated_since'?:string, 'page'?:int, 'custom_id'?:string} $params
     */
    public function list(array $params = []): Response
    {
        $allowed = ['since', 'updated_since', 'page', 'custom_id'];
        return $this->dispatcher->get('/accounts/{accountSlug}/subjects.json', $this->filterOptions($params, $allowed));
    }

    /**
     * @param array{'query'?:string, 'page'?: int} $params
     */
    public function search(array $params = []): Response
    {
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/subjects/search.json',
            $this->filterOptions($params, ['query', 'page'])
        );
    }

    public function get(int $id): Response
    {
        return $this->dispatcher->get(sprintf('/accounts/{accountSlug}/subjects/%d.json', $id));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Response
    {
        return $this->dispatcher->post('/accounts/{accountSlug}/subjects.json', $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): Response
    {
        return $this->dispatcher->patch(sprintf('/accounts/{accountSlug}/subjects/%d.json', $id), $data);
    }

    public function delete(int $id): Response
    {
        return $this->dispatcher->delete(sprintf('/accounts/{accountSlug}/subjects/%d.json', $id));
    }
}
