<?php

declare(strict_types=1);

namespace fakturoid\fakturoid_php;

// For testing purposes.
class Requester
{
    public function run($options)
    {
        $request = new Request($options);
        return $request->run();
    }
}
