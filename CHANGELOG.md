## next
- Add support for rate limit headers

## 4.0.0
- Remove user agent in favor of PSR-18 client configuration and add better documentation for ClientInterface.

## 3.0.0
- Remove support of PHP 8.1

## 2.1.0
- Add support for [Webhooks](https://www.fakturoid.cz/api/v3/webhooks)

## 2.0.1
- Fix refreshing oauth access token

## 2.0.0
- Add support for OAuth 2.0 authentication.
- Require PHP 8.1 or higher.
- Explode Client to separate providers.
- Add support for PSR-17 and PSR-18.
- Updating the entire library to support [API v3](https://www.fakturoid.cz/api/v3).

## 1.4.0
- Backport 4.0.0 for PHP 7.4 and 8.0 compatibility.

## 1.3.0

- Add support for inventory items and moves.

## 1.2.0

- Add method for fetching [invoice number formats](https://github.com/fakturoid/fakturoid-php/pull/28). (Thanks @tomas-kulhanek)

  ```php
  $response = $f->getInvoiceNumberFormats();
  ```

## 1.1.0

- Added methods for fetching [reports](https://github.com/fakturoid/fakturoid-php/commit/28a750410093ae09173ae22ad7c5e7bf64cfede1) and [bank accounts](https://github.com/fakturoid/fakturoid-php/commit/458819d2d2ab6857622695903782c78adcf8edaa). (Thanks for your contributions @RiKap and @TakeruDavis!)


## 1.0.3

- Whitelist parameters for invoice and expense fire actions.

  ```php
  $f->fireInvoice(123, 'pay', array('paid_at' => '2019-08-14', 'paid_amount' => '1200', 'variable_symbol' => '12345678', 'bank_account_id' => 23));
  $f->fireExpense(123, 'pay', array('paid_on' => '2019-08-14', 'paid_amount' => '1200', 'variable_symbol' => '12345678', 'bank_account_id' => 23));
  ```

## 1.0.2

- Fixed request caching headers `If-None-Match` and `If-Modified-Since` being case-sensitive.
- Send client PHP version in request custom header to help us know what should be
  the minimal supported version of PHP so that we can safely use newer language features
  without breaking your things.

## 1.0.1

- Fixed headers being case-sensitive.

## 1.0.0

- Minimum PHP version was raised from 5.2 to 5.3 due to the need of anonymous
  functions and namespaces.

- Classes were put under `Fakturoid` namespace.

  ```php
  // Before
  $f = new Fakturoid('..slug..', '..user@email.cz..', '..api_key..', 'PHPlib <your@email.cz>');
  try {} catch (FakturoidException $e) {}

  // After
  $f = new Fakturoid\Client('..slug..', '..user@email.cz..', '..api_key..', 'PHPlib <your@email.cz>');
  try {} catch (Fakturoid\Exception $e) {}
  ```

- Method names were changed from `underscored_names` to `camelCase` to be PSR-2 friendly.

  ```php
  // Before
  $f->create_subject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));

  // After
  $f->createSubject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));
  ```

- Methods were changed to return a `Fakturoid\Response` object instead of an array.
  This allows for more info to be available, (e.g. headers).

  ```php
  // Before
  $subject = $f->create_subject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));

  // After
  $response     = $f->createSubject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));
  $subject      = $response->getBody();
  $lastModified = $response->getHeader('Last-Modified');
  $headers      = $response->getHeaders();
  $status       = $response->getStatusCode();
  ```

## 0.1.0 (Initial version)
