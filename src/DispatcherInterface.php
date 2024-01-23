<?php

namespace Fakturoid;

interface DispatcherInterface
{
    /**
     * @param array<string, string> $queryParams
     */
    public function get(string $path, array $queryParams = []): Response;

    /**
     * @param array<string, mixed> $data
     */
    public function post(string $path, array $data = []): Response;

    /**
     * @param array<string, mixed> $data
     */
    public function patch(string $path, array $data): Response;

    public function delete(string $path): Response;
}
