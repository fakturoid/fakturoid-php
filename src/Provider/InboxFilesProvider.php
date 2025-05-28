<?php

namespace Fakturoid\Provider;

use Fakturoid\DispatcherInterface;
use Fakturoid\Response;

final class InboxFilesProvider extends Provider
{
    /**
     * @readonly
     */
    private DispatcherInterface $dispatcher;

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function list(): Response
    {
        return $this->dispatcher->get('/accounts/{accountSlug}/inbox_files.json');
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Response
    {
        return $this->dispatcher->post('/accounts/{accountSlug}/inbox_files.json', $data);
    }

    public function sendToOCR(int $id): Response
    {
        return $this->dispatcher->post(sprintf('/accounts/{accountSlug}/inbox_files/%d/send_to_ocr.json', $id));
    }

    public function download(int $id): Response
    {
        return $this->dispatcher->get(sprintf('/accounts/{accountSlug}/inbox_files/%d/download', $id));
    }

    public function delete(int $id): Response
    {
        return $this->dispatcher->delete(sprintf('/accounts/{accountSlug}/inbox_files/%d.json', $id));
    }
}
