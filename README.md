# Fakturoid PHP lib

PHP library for [Fakturoid.cz](https://www.fakturoid.cz/). Please see [API](https://fakturoid.docs.apiary.io/) for more documentation.
New account just for testing API and using separate user (created via "Nastavení > Uživatelé a oprávnění") for production usage is highly recommended.

![Tests](https://github.com/fakturoid/fakturoid-php/actions/workflows/tests.yml/badge.svg)

## Installation
The recommended way to install is through Composer:

```
composer require fakturoid/fakturoid-php
```

Library requires PHP 5.3.0 (or later) and `ext-curl` and `ext-json` extensions.

## Usage

```php
require_once '/path/to/lib/Fakturoid.php';
$f = new Fakturoid\Client('..slug..', '..user@email.cz..', '..api_key..', 'PHPlib <your@email.cz>');

// create subject
$response = $f->createSubject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));
$subject  = $response->getBody();

// create invoice with lines
$lines    = array(array('name' => 'Big sale', 'quantity' => 1, 'unit_price' => 1000));
$response = $f->createInvoice(array('subject_id' => $subject->id, 'lines' => $lines));
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

$response     = $f->getInvoice(123, array('If-None-Match' => $etag, 'If-Modified-Since' => $lastModified));
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

If you call `$f->getInvoicePdf()` right after creating an invoice, you'll get
a status code `204` (`No Content`) with empty body, this means the invoice PDF
hasn't yet been generated and you should try again a second or two later.

More info in [API docs](https://fakturoid.docs.apiary.io/#reference/invoices/invoice-pdf/stazeni-faktury-v-pdf).

```php
$invoiceId = 123;

// This is just an example, you may want to do this in a background job and be more defensive.
while (true) {
    $response = $f->getInvoicePdf($invoiceId);

    if ($response->getStatusCode() == 200) {
        $data = $response->getBody();
        file_put_contents("{$invoiceId}.pdf", $data);
        break;
    }

    sleep(1);
}
```

## Using `custom_id`

You can use `custom_id` attribute to store your application record ID into our record.
Invoices and subjects can be filtered to find a particular record:

```php
$response = $f->getSubjects(array('custom_id' => '10'));
$subjects = $response->getBody();
$subject  = null;

if (count($subjects) > 0) {
    $subject = $subjects[0];
}
```

As for subjects, Fakturoid won't let you create two records with the same `custom_id` so you don't have to worry about multiple results.
Also note that the field always returns a string.

## InventoryItem resource

To get all inventory items:

```php
$f->getInventoryItems();
```

To filter inventory items by certain SKU code or article number:

```php
$f->getInventoryItems(array('sku' => 'SKU1234'));
$f->getInventoryItems(array('article_number' => 'IAN321'));
```

To search inventory items (searches in `name`, `article_number` and `sku`):

```php
$f->searchInventoryItems(array('query' => 'Item name'));
```

To get all archived inventory items:

```php
$f->getArchivedInventoryItems();
```

To get a single inventory item:

```php
$f->getInventoryItem($inventoryItemId);
```

To create an inventory item:

```php
$data = array(
    'name' => 'Item name',
    'sku' => 'SKU12345',
    'track_quantity' => true,
    'quantity' => 100,
    'native_purchase_price' => 500,
    'native_retail_price' => 1000
);
$f->createInventoryItem($data);
```

To update an inventory item:

```php
$f->updateInventoryItem($inventoryItemId, array('name' => 'Another name'));
```

To archive an inventory item:

```php
$f->archiveInventoryItem($inventoryItemId);
```

To unarchive an inventory item:

```php
$f->unarchiveInventoryItem($inventoryItemId);
```

To delete an inventory item:

```php
$f->deleteInventoryItem($inventoryItemId);
```

## InventoryMove resource

To get get all inventory moves across all inventory items:

```php
$f->getInventoryMoves();
```

To get inventory moves for a single inventory item:

```php
$f->getInventoryMoves(array('inventory_item_id' => $inventoryItemId));
```

To get a single inventory move:

```php
$f->getInventoryMove($inventoryItemId, $inventoryMoveId);
```

To create a stock-in inventory move:

```php
$f->createInventoryMove(
    $inventoryItemId,
    array(
        'direction' => 'in',
        'moved_on' => '2023-01-12',
        'quantity_change' => 5,
        'purchase_price' => '249.99',
        'purchase_currency' => 'CZK',
        'private_note' => 'Bought with discount'
    )
)
```

To create a stock-out inventory move:

```php
$f->createInventoryMove(
    $inventoryItemId,
    array(
        'direction' => 'out',
        'moved_on' => '2023-01-12',
        'quantity_change' => '1.5',
        'retail_price' => 50,
        'retail_currency' => 'EUR',
        'native_retail_price' => '1250'
    )
);
```

To update an inventory move:

```php
$f->updateInventoryMove($inventoryItemId, $inventoryMoveId, array('moved_on' => '2023-01-11'));
```

To delete an inventory move:

```php
$f->deleteInventoryMove($inventoryItemId, $inventoryMoveId);
```

## Handling errors

Library raises `Fakturoid\Exception` if server returns code `4xx` or `5xx`. You can get response code and response body by calling `getCode()` or `getMessage()`.

```php
try {
    $subject = $f->createSubject(array('name' => '', 'email' => 'aloha@pokus.cz'));
} catch (Fakturoid\Exception $e) {
    $e->getCode(); // 422
    $e->getMessage(); // '{"errors":{"name":["je povinná položka","je příliš krátký/á/é (min. 2 znaků)"]}}'
}
```

### Common problems

- Ensure you have certificates for curl present - either globaly in `php.ini` or call `curl_setopt($ch, CURLOPT_CAINFO, "/path/to/cacert.pem")`.
- In case of problem please contact our invoicing robot on podpora@fakturoid.cz.

## Development

- To run tests, PHPUnit requires `ext-dom` extension (typically a `php-xml` package on Debian) and `ext-mbstring` extension (`php-mbstring` package).
- If you wish to generate code coverage (and have more intelligent stack traces), you will need [Xdebug](https://xdebug.org/)
  (`php-xdebug` package), it will hook itself into PHPUnit automatically.

### macOS

```sh
$ brew install php
$ brew install composer
$ arch -arm64 pecl install xdebug # For Apple M1 chips
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
$ XDEBUG_MODE=coverage composer test # Generates coverage
$ XDEBUG_MODE=coverage vendor/bin/phpunit
```

### Code-Style Check

Both commands do the same but the second version seems to have a more intelliget output.

```sh
$ composer lint
$ vendor/bin/phpcs --standard=PSR2 lib
```
