## Laravel Hyrule

 <img src="https://github.com/square/laravel-hyrule/actions/workflows/php.yml/badge.svg">

Hyrule provides an object-oriented, fluent API for building validation rules for use w/ Laravel's Validation component. This unlocks patterns that make it easier to define set of rules to enforce complex, nested data structures that is typical in API development.

**Why:**

Defining validation rules in Laravel involves manually building arrays. As business logic evolves and validation rules become more complex, those arrays grow in size, and building them also becomes more complex. Before long, you find yourself _manipulating_ arrays: adding or removing rules based on conditions, refactor segments to be re-used, etc. and over time, this pattern can feel really clunky. It doesn't take a lot to make managing validation rule definitions feel like it's getting out of control. This library aims to fix that by offers a better API that helps you for the long-term:

* Fluent API that allows you to define rules ergonomically e.g. add conditionals with ease, no more error-prone array manipulations.
* Composable: Simplifies rule-building logic that can be reused multiple times, at multiple nesting levels. No more passing down & reconstructing dot-notated prefixes.
* Strictness means less surprises: Promote enforcement of data-types, and reject unknown fields by default.

## Installation


```bash
composer require square/laravel-hyrule
```

### Setup

1.) Register the service provider:

```php
// config/app.php

return [
    // ...
    'providers' => [
       // ...
       Square\Hyrule\HyruleServiceProvider::class,
       // etc.
     ],
];
```

2.) Publish the config:

```bash
php artisan vendor:publish --provider="Square\Hyrule\HyruleServiceProvider"
```

> Using the service provider & the default config will allow your app to use [`StrictValidator`](./docs/strict-validator.md).


## API Basics

Initializing a rule-builder and adding your first field:

```php
// Initialize a new builder: it will help you build up your validation rule-set.
$builder = Hyrule::create();

// Describe your expected input:
// It needs the field name *and* the data-type.
// This creates a *Node* that you can then attach rules to:
$builder->string('first_name')
    ->required() // Attach rules. This one marks the field as required.
    ->max(255); // ...and this one says it cannot be greater than 255 chars in length.
```

Fleshing out the rest of your fields & their rules:

```php

$builder
    ->string('first_name')
        ->required()
        ->max(255)
        ->end() // Tell the builder you are done w/ this field...
    ->string('last_name') // ...so you can start a new one!
        ->required()
        ->max(255)
        ->end()
    ->integer('age') // ...This field is an integer.
        ->required()
        ->min(21)
        ->max(60)
        ->end();


$rules = $builder->build();

// $rules:
[
    '' => ['required', 'array:first_name,last_name,age'],
    'first_name' => ['string', 'required', 'max:255'],
    'last_name' => ['string', 'required', 'max:255'],
    'age' => ['integer', 'required', 'min:21', 'max:60'],
]

```


Start validating!

```php

// Compile into an array Illuminate\Validation\Validator understands.
$rules = $builder->build();

// Use it e.g.
$validator = Validator::make($data, $rules);

// ...or
$request->validate($rules);

// etc.
```

##  Fields API

Hyrule forces you to define the expected data-type for each field. It supports all ranges of types, from scalar types to non-scalar types.

### Scalar Types

Adding scalar fields are as easy as:
```php
$builder->string('product_name');
$builder->integer('quantity');
$builder->float('rating');
$builder->numeric('display_price')
$builder->boolean('on_sale');
```

### Non-Scalar Types

No matter how deep and complex your validation rules go, you can use the same set of APIs:

#### Objects

Use `->object(...)` to start defining nested fields e.g.

```php
$builder
    // "nutritional_facts" is a required field w/ a bunch of nested fields.
    ->object('nutritional_facts')
        ->required()
        // Describe the fields:
        ->integer('servings_per_container')
            ->required()
            ->min(1)
            ->end()
        ->string('serving_size')
            ->required()
            ->min(1)
            ->max(30)
            ->end()
        // "fat", a nested field, has a bunch of nested fields, too.
        ->object('fat')
            ->integer('saturated_fat_grams')->end()
            ->integer('saturated_fat_percent')
                ->max(100)
                ->end();
            ->end();

```

##### Unknown fields

By default, Hyrule helps you build robust & secure applications by only allowing fields you explicitly defined via the Fields API. This is specifically designed
to help you be intentional w/ what you expect from your data. For example, this is another mechanism by which your API can further sanitize user input.

If you expect a field to come through, the library would still require you to specify the data-type. But you don't have
to specify other rules:

```php
Hyrule::create()
    ->string('name')
      ->end()
    // etc.
```

If you'd like to allow unknown fields through, use this method on the appropriate node(s):

```php
Hyrule::create()
  ->allowUnknownProperties() // <- Allows unknown fields at root-level.
  ->object('data')
    ->allowUnknownProperties() // <- It does not carry-over. Add it to everywhere you wish to skip this.
    // etc.
```

#### Arrays of scalar values

You guessed it: Start with `->array()`:

```php
// Defines an array field named "tags"...
$builder
    ->array('tags')
        // Array length must be between 1-10 elements long:
        ->min(1)
        ->max(10)
        // ...and each element (i.e. a tag) must be a string between 3-100 chars in length.
        ->each('string')
            ->min('3')
            ->max('100');
        // etc.

```

As you can see in this example, Hyrule promotes strictness even for what goes in arays.

#### Arrays of objects

Just  define it like any other array field, and use the exact same API to define the nested fields:

```php
$builder
    // Required "skus" must be between 1-10 items.
    ->array('skus')
        ->required()
        ->min(1)
        ->max(10)
        // Each SKU in the array are objects of their own:
        ->each('object')            
            // Each SKU has these fields:
            ->string('name')
                ->required()
                ->max(255)
                ->end()
            ->integer('quantity')
                ->min(0)
                ->end()
            // etc.
```



## Rules API

First let's talk about what happens when you use the Fields API described above. When you define a field, a *node* is created & returned by the builder. You can then use the Rules API to add validation rules on a node.

##### Basic Example

```php

// Adding built-in validation rules in Laravel
$builder
    ->string('foobar') // Returns a `StringNode` for the "foobar" field.
    ->required() // Adds the "required" validation rule.
    // Supports rules that accepts parameters like:
    ->min(1) // Adds "min:1"
    ->max(255) // Adds "max:1"
    ->requiredIf('vehicle_type', 'car') // Adds "required_if:vehicle_type,car"

    // Supports rules that access multiple parameters like:
    ->in('A', 'B', 'C') // Adds "in:A,B,C"
    // etc.
```

##### Custom Rules Support

This library helps you *build* validation rule definitions & does not limit you from using custom rules that doesn't come w/ Laravel:

```php

$builder
    ->string('foobar')
        // Converts camel-case to snake-case notation:
        ->barBaz('olives', 'on', 'pizza') // Adds "bar_baz:olives,on,pizza"

        // Supports raw rule definitions:
        ->rule('required_without:another_field')

        // ... as well as custom Rule objects:
        ->rule(new MyCustomRule());
```

### What's up with `->end()`?

Once you understand that `Square\Hyrule\Builder` manages a tree of nodes and that the Fields APIs return child nodes,
all you have to know is that `->end()` returns the _parent_ of the node, and it is the fluent way of traversing back
up the tree:

```php

$builder = Hyrule::create() // The root
    ->string('name')
        ->required()
        ->end() // Brings us back to $builder, the root node
        
    // New field on root:
    ->object('characteristics')
        ->required()
        ->string('eye_color')
            ->in(...EyeColors::all())
            ->end() // Back to "characteristics"
        ->numeric('height_cm')
            ->end() // Back to "characteristics"
        ->with(...)
    ->end() // Back to $builder, the root node.
    
    // Another field on root:
    ->array('siblings')
        ->max(10)
        ->each('object') // Starts the "*" ObjectNode
            ->string('name')
            ->end() // Back to the "*" ObjectNode
        ->end() // Back to "siblings"
    ->end() // Back to $builder, the root node.

    // etc.
```

If you are not a fan of this, you can use the [Fields-With API](./docs/fields-with.md).


## Advanced Topics

- [Fields-With API](./docs/fields-with.md)
- [Apply rules on entire data itself with **`StrictValidator`**](./docs/strict-validator.md)
- [Reusable builder functions with **`->with(...)`**](./docs/builder-functions-with.md)
- **Built-in helper builder functions**
    - [**`Hyrule::if(...)`**](./docs/hyrule-if.md)
    - [**`Hyrule::requiredIf(...)`**](./docs/hyrule-required-if.md)
- [Path Expressions with **`Hyrule::pathExp()`**](./docs/path-expressions.md)
- [Custom validation for shape of an object](./docs/custom-properties-rule.md)
- [Static analysis w/ PHPStan](./docs/static-analysis-phpstan.md)

