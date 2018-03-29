## Soon-to-be-1.0.0

- Methods now return a `FakturoidResponse` object instead of an array.

  ```php
  // Before
  $response     = $f->create_subject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));
  $subject      = $response->getBody();
  $lastModified = $response->getHeader('Last-Modified');
  $headers      = $response->getHeaders();
  $status       = $response->getStatusCode();

  // After
  $subject = $f->create_subject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));
  ```

## 0.1.0 (Initial version)
