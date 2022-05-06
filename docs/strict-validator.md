## `StrictValidator`

Hyrule helps you build robust & secure applications by only allowing fields you explicitly defined via the [Fields API](../README.md#fields-api) or [Fields-With API](./fields-with.md).

Hyrule enforces this by using the `Square\Hyrule\Validation\Rules\KnownPropertiesOnly` rule which it adds to all `object(...)`  nodes.

This works for any `ObjectNode` nested in the input data, but will not work on the top-most level because Laravel does not support setting & applying rules on the entire
data the way it does with the rest. ([Link to rejected proposal PR](https://github.com/laravel/framework/pull/41962)).

`StrictValidator` solves that problem by allowing rules to be set & applied to the input data itself:

```php
$rules = [
    '' => [new KnownPropertiesOnly('foo', 'bar')], // <- StrictValidator understands this.
    'foo' => [ ... ],
    'bar' => [ ..., new KnownPropertiesOnly(...) ],
    'bar.baz' => [ ... ],
];
```
The package will automatically configure the framework to use this validator variant if you use the default `hyrule` config.

If you wish to disable this behavior:

```php
// config/hyrule.php:

return [
    // To disable, set this to a falsy value:
    'use_strict_validator_class' => false,
];

```

If you already use your own variant of `Illuminate\Validation\Validator` but don't want to miss out on top-most level enforcement
of white-listed fields, you can pull the trait & register your variant:

```php

// app/Validation/Validator.php

namespace App\Validation\Validator;

use Illuminate\Validation\Validator;
use Square\Hyrule\Validation\ValidatesTopLevelRules;

class Validator extends BaseValidator
{
    use ValidatesTopLevelRules;
    
    // Rest of your custom code...
}
```

```php
// config/hyrule.php

return [
    // Set it to your custom validator's FQCN:
    'use_strict_validator_class' => App\Validation\Validator::class,
]
```




