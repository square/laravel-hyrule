### `Hyrule::if()`

Applies a builder function if a condition is met:

```php
$builder
    ->integer('quantity');
        ->with(Hyrule::if($lowStock, static function (IntegerNode $qty) {
            $qty->max(2)
                ->rule(new HasntOrderedWithinLast7DaysRule());
        }))
        ->required();
        ->end()
    ->string('...') // etc.
