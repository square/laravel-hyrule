## `Hyrule::requiredIf()`

Marks a field as required if a condition is met:

```php
$builder->integer('price')
    ->with(Hyrule::requiredIf($customAmountsAllowed))
    ->min(1000)
    ->max(50000);
```

This is a shortcut for:

```php
->with(Hyrule::if($expression, static fn($node) => $node->required()))
```


Not to be confused w/ `$node->requiredIf(...)`, which would add a `"required_if:..."` rule.

