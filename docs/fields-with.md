## Fields-With API

If you are not a fan of the  aliteration of `->end()` when building your rule-set, you may find this alternate API
more to your liking:

```php

$allowedCurrencies = [...];

$builder = Hyrule::create()
    ->stringWith('product_name', static function(StringNode $fn) {
        $fn->required()
            ->max(255);
    })
    ->stringWith('currency', static function (StringNode $currency) use ($allowedCurrencies) {
        $currency->required()
            ->in($allowedCurrencies);
    })
    ->objectWith('dimensions', static function (ObjectNode $dimensions) {
        $dimensions->floatWith('width',  ...);
        $dimensions->floatWith('height', ...);
        $dimensions->floatWith('length', ...);
    });
```

It's still a fluent API, and you might prefer not having to explicitly `->end()` to traverse back up the tree.

The drawback of course is that you have isolated scopes and may have to resort to using `use(...)` in callbacks, and
this would produce more indentations than the main counterparts.

We think that those that benefit from this library are those that deal with a lot of nested fields, and the 
main Fields API avoids a lot of closure-related boilerplates in exchange for `->end()` aliteration. But the choice is yours.
