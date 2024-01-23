<?php

namespace Fakturoid\Enum;

enum AuthTypeEnum: string
{
    case AUTHORIZATION_CODE_FLOW = 'authorization_code';
    case CLIENT_CREDENTIALS_CODE_FLOW = 'client_credentials';
}
