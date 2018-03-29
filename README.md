# Fakturoid PHP lib

PHP library for [Fakturoid.cz](https://www.fakturoid.cz/). Please see [API](http://docs.fakturoid.apiary.io/) for more documentation.
New account just for testing API and using separate user (created via "Nastavení > Uživatelé a oprávnění") for production usage is highly recommended.

## Install
The recommended way to install is through Composer:

```
composer require fakturoid/fakturoid-php
```

Library requires PHP 5.3.0 (or later) and `ext-curl` and `ext-json` extensions.

## Usage

```php
require_once '/path/to/lib/Fakturoid.php';
$f = new Fakturoid('..slug..', '..user@email.cz..', '..api_key..', 'PHPlib <your@email.cz>');

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

## Handling errors

Library raises `FakturoidException` if server returns code `4xx` or `5xx`. You can get response code and response body by calling `getCode()` or `getMessage()`.

```php
try {
    $subject = $f->createSubject(array('name' => '', 'email' => 'aloha@pokus.cz'));
} catch (FakturoidException $e) {
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
$ brew tap homebrew/php
$ brew install composer
$ brew install php72-xdebug # This will also install Homebrew's PHP 7.2
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
$ vendor/bin/phpcs --standard=PSR2 lib
```