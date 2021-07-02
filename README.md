# Fakturoid PHP lib

PHP library for [Fakturoid.cz](https://www.fakturoid.cz/). Please see [API](https://fakturoid.docs.apiary.io/) for more
documentation. New account just for testing API and using separate user (created via "Nastavení > Uživatelé a
oprávnění") for production usage is highly recommended.

[![Circle CI](https://circleci.com/gh/fakturoid/fakturoid-php.svg?style=svg)](https://circleci.com/gh/fakturoid/fakturoid-php)

## Installation

The recommended way to install is through Composer:

```
composer require fakturoid/fakturoid-php
```

Library requires PHP 7.4 (or later) and `ext-curl` and `ext-json` extensions.

## Usage

```php
require_once __DIR__ . '/vendor/autoload.php';
use fakturoid\fakturoid_php\Client;
$f = new Client('..slug..', '..user@email.cz..', '..api_key..', 'PHPlib <your@email.cz>');

// create subject
$response = $f->createSubject(['name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz']);
$subject  = $response->getBody();

// create invoice with lines
$lines    = ['name' => 'Big sale', 'quantity' => 1, 'unit_price' => 1000];
$response = $f->createInvoice(['subject_id' => $subject->id, 'lines' => $lines]);
$invoice  = $response->getBody();

// send created invoice
$f->fireInvoice($invoice->id, 'deliver');

// to mark invoice as paid
$f->fireInvoice($invoice->id, 'pay'); // or 'pay_proforma' for paying proforma and 'pay_partial_proforma' for partial proforma

// you can also take advantage of caching (via ETag and Last-Modified headers).
$response     = $f->getInvoice(123);
$status       = $response->getStatusCode();            // 200
$invoice      = $response->getBody();                  // stdClass Object
$etag         = $response->getHeader('ETag');          // 'W/"6e0d839fb2edb9eadcd9ecda2d227c96"'
$lastModified = $response->getHeader('Last-Modified'); // "Wed, 28 Mar 2018 03:11:14 GMT"

$response     = $f->getInvoice(123, ['If-None-Match' => $etag, 'If-Modified-Since' => $lastModified]);
$status       = $response->getStatusCode();            // 304 Not Modified
$invoice      = $response->getBody();                  // null
```

## Downloading an invoice PDF

```php
$invoiceId = 123;
$response = $f->getInvoicePdf($invoiceId);
$data = $response->getBody();
file_put_contents("{$invoiceId}.pdf", $data);
```

If you call `$f->getInvoicePdf()` right after creating an invoice, you'll get a status code `204` (`No Content`) with
empty body, this means the invoice PDF hasn't yet been generated and you should try again a second or two later.

More info in [API docs](https://fakturoid.docs.apiary.io/#reference/invoices/invoice-pdf/stazeni-faktury-v-pdf).

```php
$invoiceId = 123;

// This is just an example, you may want to do this in a background job and be more defensive.
while (true) {
  $response = $f->getInvoicePdf($invoiceId);

  if ($response->getStatusCode() === 200) {
    $data = $response->getBody();
    file_put_contents("{$invoiceId}.pdf", $data);
    break;
  }

  sleep(1);
}
```

## Using `custom_id`

You can use `custom_id` attribute to store your application record ID into our record. Invoices and subjects can be
filtered to find a particular record:

```php
$response = $f->getSubjects(['custom_id' => '10']);
$subjects = $response->getBody();
$subject  = null;

if (count($subjects)) {
    $subject = $subjects[0];
}
```

As for subjects, Fakturoid won't let you create two records with the same `custom_id` so you don't have to worry about
multiple results. Also note that the field always returns a string.

## Handling errors

Library raises `Fakturoid\Exception` if server returns code `4xx` or `5xx`. You can get response code and response body
by calling `getCode()` or `getMessage()`.

```php
try {
    $subject = $f->createSubject(['name' => '', 'email' => 'aloha@pokus.cz']);
} catch (\fakturoid\fakturoid_php\Exception $e) {
    $e->getCode(); // 422
    $e->getMessage(); // '{"errors":{"name":["je povinná položka","je příliš krátký/á/é (min. 2 znaků)"]}}'
}
```

### Common problems

- Ensure you have certificates for curl present - either globaly in `php.ini` or
  call `curl_setopt($ch, CURLOPT_CAINFO, "/path/to/cacert.pem")`.
- In case of problem please contact our invoicing robot on podpora@fakturoid.cz.

## Development

- To run tests, PHPUnit requires `ext-dom` extension (typically a `php-xml` package on Debian) and `ext-mbstring`
  extension (`php-mbstring` package).
- If you wish to generate code coverage (and have more intelligent stack traces), you will
  need [Xdebug](https://xdebug.org/)
  (`php-xdebug` package), it will hook itself into PHPUnit automatically.

### macOS

```sh
$ brew install composer
# Reload terminal
$ composer install
```

### Debian

```sh
$ sudo aptitude install php php-curl php-xml php-mbstring php-xdebug composer
$ composer install
```

### Testing

Both commands do the same but the second version is a bit faster.

```sh
$ composer test
$ vendor/bin/phpunit
```

### Code-Style Check

Both commands do the same but the second version seems to have a more intelliget output.

```sh
$ composer lint
$ vendor/bin/phpcs --standard=PSR12 lib
```
