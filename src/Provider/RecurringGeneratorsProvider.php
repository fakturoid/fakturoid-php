<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class RecurringGeneratorsProvider extends Provider
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
     * @param array{
     *  'since'?:string,
     *  'updated_since'?:string,
     *  'page'?:int,
     *  'subject_id'?:int
     * } $params
     */
    public function list(array $params = []): Response
    {
        $allowed = ['since', 'updated_since', 'page', 'subject_id'];
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/recurring_generators.json',
            $this->filterOptions($params, $allowed)
        );
    }

    public function get(int $id): Response
    {
        return $this->dispatcher->get(sprintf('/accounts/{accountSlug}/recurring_generators/%d.json', $id));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data = []): Response
    {
        return $this->dispatcher->post('/accounts/{accountSlug}/recurring_generators.json', $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data = []): Response
    {
        return $this->dispatcher->patch(sprintf('/accounts/{accountSlug}/recurring_generators/%d.json', $id), $data);
    }

    public function delete(int $id): Response
    {
        return $this->dispatcher->delete(sprintf('/accounts/{accountSlug}/recurring_generators/%d.json', $id));
    }
}
