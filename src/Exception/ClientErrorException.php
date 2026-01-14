<?php

namespace Fakturoid\Exception;

class ClientErrorException extends RequestException
{
    public function isRateLimitExceeded(): bool
    {
        return $this->getResponse()->getStatusCode() === 429;
    }
}
