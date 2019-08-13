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
