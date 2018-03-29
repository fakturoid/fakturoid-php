## Soon-to-be-1.0.0

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
  $response     = $f->createSubject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));
  $subject      = $response->getBody();
  $lastModified = $response->getHeader('Last-Modified');
  $headers      = $response->getHeaders();
  $status       = $response->getStatusCode();

  // After
  $subject = $f->create_subject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));
  ```

## 0.1.0 (Initial version)
