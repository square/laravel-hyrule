## Laravel Hyrule

### PHP 7.4 Support

Support for PHP 7.4 will be limited to `v1.x` of this package. We will do our absolute best to have *zero* API *usage* differences between `v1.x` and `v2.x`.

However we cannot guarantee backwards-compatibility if you extend the classes & override methods, as upgrading to `v2` would require you to update the method signatures of overridden methods (e.g. union-type support).

#### Installation

Install via Composer:

```bash
composer require square/laravel-hyrule:^1.0
```

Please refer to the API usage documentation on the mainline branch.
