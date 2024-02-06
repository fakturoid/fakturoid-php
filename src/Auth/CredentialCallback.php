<?php

namespace Fakturoid\Auth;

interface CredentialCallback
{
    public function __invoke(?Credentials $credentials = null): void;
}
