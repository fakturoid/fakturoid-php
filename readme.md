# Fakturoid PHP lib

PHP library for [Fakturoid.cz](https://www.fakturoid.cz/). Please see [API](http://docs.fakturoid.apiary.io/).

## Usage

```php
require_once 'fakturoid.php';
$f = new Fakturoid('..subdomain..', '..user@email.cz..', '..api_key..', 'PHPlib <your@email.cz>');
$subject = $f->create_subject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));
$lines   = array(array('name' => 'Big sale', 'quantity' => 1, 'unit_price' => 1000));
$invoice = $f->create_invoice(array('subject_id' => $subject->id, 'lines' => $lines));
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

## FAQ:

### cURL returns FALSE?
- Set ```CURLOPT_SSL_VERIFYPEER``` to ```FALSE```
- Or globally in php.ini:
    ```curl.cainfo = "ABSOLUTE_PATH\cacert.pem"```

    **Certificate is available to download here:**
    http://curl.haxx.se/ca/cacert.pem 

    **For more information about this problem:**
    http://stackoverflow.com/questions/18971983/curl-requires-curlopt-ssl-verifypeer-false