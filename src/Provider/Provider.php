<?php

namespace Fakturoid\Provider;

abstract class Provider
{
    /**
     * @param array<string, mixed> $options
     * @param array<string> $allowedKeys
     * @return array<string, mixed>
     */
    protected function filterOptions(array $options, array $allowedKeys = []): array
    {
        if ($options === []) {
            return [];
        }

        $unknownKeys = [];

        foreach ($options as $key => $value) {
            if (!in_array(strtolower($key), $allowedKeys)) {
                unset($options[$key]);
                $unknownKeys[] = $key;
            }
        }

        if (!empty($unknownKeys)) {
            trigger_error('Unknown option keys: ' . implode(', ', $unknownKeys));
        }

        return $options;
    }
}
