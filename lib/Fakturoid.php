<?php

/* Safety */

require_once 'Fakturoid/Client.php';
require_once 'Fakturoid/Requester.php';
require_once 'Fakturoid/Request.php';
require_once 'Fakturoid/Response.php';
require_once 'Fakturoid/Exception.php';

if (!function_exists('curl_init')) {
    throw new Fakturoid\Exception('Fakturoid lib needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Fakturoid\Exception('Fakturoid lib needs the JSON PHP extension.');
}
