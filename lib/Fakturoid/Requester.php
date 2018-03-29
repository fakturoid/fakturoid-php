<?php

namespace Fakturoid;

// For testing purposes.
class Requester
{
    public function run($options)
    {
        $request  = new Request($options);
        $response = $request->run();

        return $response;
    }
}
