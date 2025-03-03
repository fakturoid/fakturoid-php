<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class ExpensesProvider extends Provider
{
    private DispatcherInterface $dispatcher;

    public function __construct(
        DispatcherInterface $dispatcher
    ) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param array{
     *  'since'?:string,
     *  'updated_since'?:string,
     *  'page'?:int,
     *  'subject_id'?:int,
     *  'custom_id'?:string,
     *  'number'?:string,
     *  'variable_symbol'?:string,
     *  'status'?:string
     * } $params
     */
    public function list(array $params = []): Response
    {
        $allowed = ['since', 'updated_since', 'page', 'subject_id', 'custom_id', 'number', 'variable_symbol', 'status'];
        return $this->dispatcher->get('/accounts/{accountSlug}/expenses.json', $this->filterOptions($params, $allowed));
    }

    /**
     * @param array{'query'?:string, 'page'?:int, 'tags'?:string[]} $params
     */
    public function search(array $params = []): Response
    {
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/expenses/search.json',
            $this->filterOptions($params, ['query', 'page', 'tags'])
        );
    }

    public function get(int $id): Response
    {
        return $this->dispatcher->get(sprintf('/accounts/{accountSlug}/expenses/%d.json', $id));
    }

    public function getAttachment(int $expenseId, int $attachmentId): Response
    {
        return $this->dispatcher->get(
            sprintf(
                '/accounts/{accountSlug}/expenses/%d/attachments/%d/download',
                $expenseId,
                $attachmentId
            )
        );
    }

    public function fireAction(int $id, string $event): Response
    {
        return $this->dispatcher->post(
            sprintf(
                '/accounts/{accountSlug}/expenses/%d/fire.json',
                $id
            ),
            ['event' => $event]
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Response
    {
        return $this->dispatcher->post('/accounts/{accountSlug}/expenses.json', $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): Response
    {
        return $this->dispatcher->patch(sprintf('/accounts/{accountSlug}/expenses/%d.json', $id), $data);
    }

    public function delete(int $id): Response
    {
        return $this->dispatcher->delete(sprintf('/accounts/{accountSlug}/expenses/%d.json', $id));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createPayment(int $expenseId, array $data = []): Response
    {
        return $this->dispatcher->post(sprintf('/accounts/{accountSlug}/expenses/%d/payments.json', $expenseId), $data);
    }

    public function deletePayment(int $expenseId, int $id): Response
    {
        return $this->dispatcher->delete(
            sprintf(
                '/accounts/{accountSlug}/expenses/%d/payments/%d.json',
                $expenseId,
                $id
            )
        );
    }
}
