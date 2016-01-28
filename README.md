# PHP Ongkir API

PHP Class ongkos kirim [Tiki](http://tiki-online.com) &amp; [JNE](http://jne.co.id) dengan menggunakan [cURL](https://php.net/manual/en/book.curl.php) dan [DiDOM](https://github.com/Imangazaliev/DiDOM).

## Requirement

- [PHP 5.4+](https://secure.php.net/supported-versions.php)
- [Multibyte String](https://php.net/manual/en/book.mbstring.php)

## Install

- Clone repo : ``git clone https://github.com/bachors/Ongkir-API.git``
- Load dependencies : ``php composer install``

## Example

```php
require_once 'vendor/autoload.php';

$ongkir = new Bachor\Ongkir('jakarta', 'padang', 10);

echo $ongkir->tiki(); // ongkir tiki

echo $ongkir->jne(); // ongkir jne
```

## License

This project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
