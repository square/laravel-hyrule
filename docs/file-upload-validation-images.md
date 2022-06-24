## File Upload Validation - Images

Here are a few examples for how you can validate image uploads.

### Validate dimensions

```php

$builder = Hyrule::create()
    ->file('avatar')
        ->required()
        ->image()   // Validates that upload is an image.
        ->dimensions()  // Starts dimension constraints...
            ->ratio(1)
            ->maxWidth(1000)
            ->end() // Ends dimension rule-set.
        ->end() // Ends the "avatar" field.
    // ... 

```

See [`Dimensions`](https://github.com/laravel/framework/blob/9.x/src/Illuminate/Validation/Rules/Dimensions.php) class for all available constraints.

### Only accept subset of image types

```php
$builder = Hyrule::create()
    ->file('avatar')
        ->required()
        ->mimeType() // Starts MIME-type constriants...
            ->image('jpeg', 'gif', 'png') // Only accept image/{jpeg,gif,png}
            ->end() // End MIME-Type constraints.
        ->end() // End the "avatar" field.
    // ... 
```

See [File Upload Validation - MIME Types](./file-upload-validation-mime-types.md) for a comprehensive guide on MIME-Type rules.