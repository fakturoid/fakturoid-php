# Fakturoid PHP lib

PHP library for [Fakturoid.cz](https://www.fakturoid.cz/). Please see [API](https://github.com/fakturoid/api).

## Usage

```php
require_once 'fakturoid.php';
$f = new Fakturoid('..subdomain..', '..api_key..', 'PHPlib <your@email.cz>');
$subject = $f->create_subject(array('name' => 'Firma s.r.o.', 'email' => 'aloha@pokus.cz'));
$lines   = array(array('name' => 'Big sale', 'quantity' => 1, 'unit_price' => 1000));
$invoice = $f->create_invoice(array('subject_id' => $subject->id, 'lines' => $lines));
```