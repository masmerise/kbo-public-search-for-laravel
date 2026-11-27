<p align="center">
<img src="https://economie.fgov.be/sites/default/files/inline-images/Entreprises/LOGO-KBO-baseline-NL-250.jpg" alt="KBO Public Search PHP SDK" height="60">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" alt="Laravel" height="50">
</p>

<p align="center">
<a href="https://github.com/masmerise/kbo-public-search-for-laravel/actions"><img src="https://github.com/masmerise/kbo-public-search-for-laravel/actions/workflows/test.yml/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/masmerise/kbo-public-search-for-laravel"><img src="https://img.shields.io/packagist/dt/masmerise/kbo-public-search-for-laravel" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/masmerise/kbo-public-search-for-laravel"><img src="https://img.shields.io/packagist/v/masmerise/kbo-public-search-for-laravel" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/masmerise/kbo-public-search-for-laravel"><img src="https://img.shields.io/packagist/l/masmerise/kbo-public-search-for-laravel" alt="License"></a>
</p>

## Laravel adapter for the KBO Public Search SDK

This package provides convenient access to the [KBO Public Search SDK](https://github.com/masmerise/kbo-public-search-php-sdk) using the Laravel framework.

## Installation

You can install the package via [composer](https://getcomposer.org):

```bash
composer require masmerise/kbo-public-search-for-laravel
```

After that, define your `kbo` credentials inside the `config/services.php` configuration file:

```php
'kbo' => [
    'username' => env('KBO_USERNAME'),
    'password' => env('KBO_PASSWORD'),
    'env' => env('KBO_ENV', 'production'), // or 'test'
],
```

Set `env` to `test` to send requests to the KBO acceptance environment instead of production. Each environment has its own credentials.

## Usage

```php
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;

$enterprise = kbo()->enterprises->find(EnterpriseNumber::fromString('0314.595.348'))->enterprise;
```

```php
$kbo = app('kbo');

$codes = $kbo->codeLists->authorizationSearchCodes()->authorizationSearchCodes;
```

```php
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\KboPublicSearch;

final readonly class EnterpriseController
{
    public function __construct(private KboPublicSearch $kbo) {}

    public function show(string $number): View
    {
        $number = EnterpriseNumber::fromString($number);

        return view('enterprise.show', [
            'enterprise' => $this->kbo->enterprises->find($number)->enterprise,
        ]);
    }
}
```

## Localization

Descriptions follow the application locale: `fr` returns French descriptions, any other locale returns Dutch, since KBO
only serves those two. Changing the locale at runtime (`App::setLocale()`) updates the resolved instance as well.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email support@masmerise.be instead of using the issue tracker.

## Credits

- [Muhammed Sari](https://github.com/mabdullahsari)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
