<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class InvoiceProvider extends Provider
{
    public function __construct(
        private readonly DispatcherInterface $dispatcher
    ) {
    }

    /**
     * @param array{
     * 'since'?:string,
     * 'until'?:string,
     * 'updated_since'?:string,
     * 'updated_until'?:string,
     * 'page'?:int,
     * 'subject_id'?:int,
     * 'custom_id'?:string,
     * 'number'?:string,
     * 'status'?:string,
     * 'document_type'?:string
     * } $params
     */
    public function list(array $params = []): Response
    {
        $allowed = [
            'since',
            'until',
            'updated_since',
            'updated_until',
            'page',
            'subject_id',
            'custom_id',
            'number',
            'status',
            'document_type',
        ];
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/invoices.json',
            $this->filterOptions($params, $allowed)
        );
    }

    /**
     * @param array{'query'?:string, 'page'?:int, 'tags'?:string[]} $params
     * @return Response
     */
    public function search(array $params = []): Response
    {
        return $this->dispatcher->get(
            '/accounts/{accountSlug}/invoices/search.json',
            $this->filterOptions($params, ['query', 'page', 'tags'])
        );
    }

    public function get(int $id): Response
    {
        return $this->dispatcher->get(sprintf('/accounts/{accountSlug}/invoices/%d.json', $id));
    }

    public function getPdf(int $id): Response
    {
        return $this->dispatcher->get(sprintf('/accounts/{accountSlug}/invoices/%d/download.pdf', $id));
    }

    public function getAttachment(int $invoiceId, int $id): Response
    {
        return $this->dispatcher->get(
            sprintf(
                '/accounts/{accountSlug}/invoices/%d/attachments/%d/download',
                $invoiceId,
                $id
            )
        );
    }

    public function fireAction(int $id, string $event): Response
    {
        return $this->dispatcher->post(
            sprintf(
                '/accounts/{accountSlug}/invoices/%d/fire.json',
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
        return $this->dispatcher->post('/accounts/{accountSlug}/invoices.json', $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): Response
    {
        return $this->dispatcher->patch(sprintf('/accounts/{accountSlug}/invoices/%d.json', $id), $data);
    }

    public function delete(int $id): Response
    {
        return $this->dispatcher->delete(sprintf('/accounts/{accountSlug}/invoices/%d.json', $id));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createPayment(int $invoiceId, array $data): Response
    {
        return $this->dispatcher->post(
            sprintf(
                '/accounts/{accountSlug}/invoices/%d/payments.json',
                $invoiceId
            ),
            $data
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createTaxDocument(int $invoiceId, int $paymentId, array $data): Response
    {
        return $this->dispatcher->post(
            sprintf(
                '/accounts/{accountSlug}/invoices/%d/payments/%d/create_tax_document.json',
                $invoiceId,
                $paymentId
            ),
            $data
        );
    }

    public function deletePayment(int $invoiceId, int $paymentId): Response
    {
        return $this->dispatcher->delete(
            sprintf(
                '/accounts/{accountSlug}/invoices/%d/payments/%d.json',
                $invoiceId,
                $paymentId
            )
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createMessage(int $invoiceId, array $data): Response
    {
        return $this->dispatcher->post(
            sprintf(
                '/accounts/{accountSlug}/invoices/%d/message.json',
                $invoiceId
            ),
            $data
        );
    }
}
