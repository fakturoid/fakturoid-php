<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class EventsProvider extends Provider
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
     * @param array{'subject_id'?: int, 'since'?: string, 'page'?: int} $params
     */
    public function list(array $params = []): Response
    {
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/events.json',
            $this->filterOptions($params, ['subject_id', 'since', 'page'])
        );
    }

    /**
     * @param array{'subject_id'?: int, 'since'?: string, 'page'?: int} $params
     */
    public function listPaid(array $params = []): Response
    {
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/events/paid.json',
            $this->filterOptions($params, ['subject_id', 'since', 'page'])
        );
    }
}
