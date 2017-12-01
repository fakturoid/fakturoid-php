# Fakturoid PHP lib

PHP library for [Fakturoid.cz](https://www.fakturoid.cz/). Please see [API](http://docs.fakturoid.apiary.io/) for more documentation. New account just for testing API and using separate user (created via "Nastavení > Uživatelé a oprávnění") for production usage is highly recommended.

## Install
The recommended way to install is through Composer:

```
composer require fakturoid/fakturoid-php
```

Library requires PHP 5.2.0 and PHP extension: `curl` and `json`.

## Usage

```php
require_once '/path/to/lib/Fakturoid.php';
$f = new Fakturoid('..slug..', '..user@email.cz..', '..api_key..', 'PHPlib <your@email.cz>');

// create subject
$subject = $f->create_subject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));

// create invoice with lines
$lines   = array(array('name' => 'Big sale', 'quantity' => 1, 'unit_price' => 1000));
$invoice = $f->create_invoice(array('subject_id' => $subject->id, 'lines' => $lines));

// send created invoice
$f->fire_invoice($invoice->id, 'deliver');

// to mark invoice as paid
$f->fire_invoice($invoice->id, 'pay'); // or 'pay_proforma' for paying proforma and 'pay_partial_proforma' for partial proforma

```

## Handling errors

Library raises `FakturoidException` if server returns code `4xx` or `5xx`. You can get response code and response body by calling `getCode()` or `getMessage()`.

```php
try {
  $subject = $f->create_subject(array('name' => '', 'email' => 'aloha@pokus.cz'));
} catch (FakturoidException $e) {
  $e->getCode(); // 422
  $e->getMessage(); // '{"errors":{"name":["je povinná položka","je příliš krátký/á/é (min. 2 znaků)"]}}'
}
```

### Common problems

- ensure you have certificates for curl present - either globaly in `php.ini` or call `curl_setopt($ch, CURLOPT_CAINFO, "/path/to/cacert.pem")`
- in case of problem please contact our invoicing robot on podpora@fakturoid.cz
