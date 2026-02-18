<?php

namespace Fakturoid\Provider;

abstract class Provider
{
    /**
     * @template TKey of string
     * @template TValue of scalar|scalar[]
     * @param array<string, TValue> $options
     * @param list<TKey> $allowedKeys
     * @return array<TKey, TValue>
     */
    protected function filterOptions(array $options, array $allowedKeys = []): array
    {
        if ($options === []) {
            return [];
        }

        $allowedMap = array_flip($allowedKeys);
        $options = array_change_key_case($options, CASE_LOWER);

        $result = [];
        $unknownKeys = [];

        foreach ($options as $key => $value) {
            if (!array_key_exists($key, $allowedMap)) {
                $unknownKeys[] = $key;
            } else {
                /** @var TKey $key */
                $result[$key] = $value;
            }
        }

        if ($unknownKeys !== []) {
            trigger_error('Unknown option keys: ' . implode(', ', $unknownKeys));
        }

        return $result;
    }
}
