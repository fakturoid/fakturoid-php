<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class InventoryItemsProvider extends Provider
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
     *  'until'?:string,
     *  'updated_since'?:string,
     *  'updated_until'?:string,
     *  'page'?:int,
     *  'article_number'?:int,
     *  'sku'?:string
     * } $params
     */
    public function list(array $params = []): Response
    {
        $allowed = ['since', 'until', 'updated_since', 'updated_until', 'page', 'article_number', 'sku'];
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/inventory_items.json',
            $this->filterOptions($params, $allowed)
        );
    }

    /**
     * @param array{
     *  'since'?:string,
     *  'until'?:string,
     *  'updated_since'?:string,
     *  'updated_until'?:string,
     *  'page'?:int,
     *  'article_number'?:int,
     *  'sku'?:string
     * } $params
     */
    public function listArchived(array $params = []): Response
    {
        $allowed = ['since', 'until', 'updated_since', 'updated_until', 'page', 'article_number', 'sku'];
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/inventory_items/archived.json',
            $this->filterOptions($params, $allowed)
        );
    }

    /**
     * @param array{'page'?:int} $params
     */
    public function listLowQuantity(array $params = []): Response
    {
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/inventory_items/low_quantity.json',
            $this->filterOptions($params, ['page'])
        );
    }

    /**
     * @param array{'query'?:string, 'page'?:int} $params
     */
    public function search(array $params = []): Response
    {
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/inventory_items/search.json',
            $this->filterOptions($params, ['query', 'page'])
        );
    }

    public function get(int $id): Response
    {
        return $this->dispatcher->get(sprintf('/accounts/{accountSlug}/inventory_items/%d.json', $id));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Response
    {
        return $this->dispatcher->post('/accounts/{accountSlug}/inventory_items.json', $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): Response
    {
        return $this->dispatcher->patch(sprintf('/accounts/{accountSlug}/inventory_items/%d.json', $id), $data);
    }

    public function delete(int $id): Response
    {
        return $this->dispatcher->delete(sprintf('/accounts/{accountSlug}/inventory_items/%d.json', $id));
    }

    public function archive(int $id): Response
    {
        return $this->dispatcher->post(
            sprintf(
                '/accounts/{accountSlug}/inventory_items/%d/archive.json',
                $id
            )
        );
    }

    public function unArchive(int $id): Response
    {
        return $this->dispatcher->post(
            sprintf(
                '/accounts/{accountSlug}/inventory_items/%d/unarchive.json',
                $id
            )
        );
    }
}
