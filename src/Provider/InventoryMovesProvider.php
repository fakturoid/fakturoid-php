<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class InventoryMovesProvider extends Provider
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
     *  'inventory_item_id'?:int
     * } $params
     */
    public function list(array $params = []): Response
    {
        $allowed = ['since', 'until', 'updated_since', 'updated_until', 'page', 'inventory_item_id'];
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/inventory_moves.json',
            $this->filterOptions($params, $allowed)
        );
    }

    public function get(int $inventoryItemId, int $moveId): Response
    {
        return $this->dispatcher->get(
            sprintf(
                '/accounts/{accountSlug}/inventory_items/%d/inventory_moves/%d.json',
                $inventoryItemId,
                $moveId
            )
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(int $inventoryItemId, array $data): Response
    {
        return $this->dispatcher->post(
            sprintf('/accounts/{accountSlug}/inventory_items/%d/inventory_moves.json', $inventoryItemId),
            $data
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $inventoryItemId, int $moveId, array $data): Response
    {
        return $this->dispatcher->patch(
            sprintf(
                '/accounts/{accountSlug}/inventory_items/%d/inventory_moves/%d.json',
                $inventoryItemId,
                $moveId
            ),
            $data
        );
    }

    public function delete(int $inventoryItemId, int $moveId): Response
    {
        return $this->dispatcher->delete(
            sprintf(
                '/accounts/{accountSlug}/inventory_items/%d/inventory_moves/%d.json',
                $inventoryItemId,
                $moveId
            )
        );
    }
}
