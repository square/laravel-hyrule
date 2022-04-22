# Reusable builder functions w/ `->with(...)`

The `->with(...)` method in a node offers you the flexibility you need to specify builder logic that can be re-used wherever you need it.

Here is an example:

#### Reuse a validation rule-set at multiple levels

```php
// A closure that attach fields & rules to an ObjectNode:
$buildPerson = function (ObjectNode $person) {
    $person
        ->string('name')
            ->required()
            ->max(255)
            ->end()
        ->integer('age')
            ->required()
            ->max(60)
            ->end()
        ->string('birth_date')
            ->optional()
            ->dateFormat('Y-m-d')
            ->end();
}

$rules = Hryule::create()
    ->object('person')
        // Apply the callable to the "person" field...
        ->with($buildPerson)
        ->array('siblings')
            ->required()
            ->max(5)
            // ... as well as for each element in the "siblings" array.
            ->each('object')
                ->with($buildPerson)
            ->end()
        ->end()
    ->end()
->build();

// returns:

[
    'person' => ['array:name,age,birth_date,siblings'],
    'person.name' => ['string', 'required', 'max:255'],
    'person.age' => ['integer', 'required', 'max:60'],
    'person.birth_date' => ['string', 'optional', 'date_format:Y-m-d'],

    'person.siblings' => ['array',  'required', 'max:5'],

    'person.siblings.*' => ['array:name,age,birth_date'],
    'person.siblings.*.name' => ['string', 'required', 'max:255'],
    'person.siblings.*.age' => ['integer', 'required', 'max:60'],
    'person.siblings.*.birth_date' => ['string', 'optional', 'date_format:Y-m-d'],
]

```

The `->with(...)` method accepts any form of callable i.e. closures, traditional `callable` notations (e.g. `[$this, 'methodName']`), `__invoke` magic method on an object, etc.

The callable will receive the node instance where `->with(...)` is called from. In our example, it is `person` and `person.siblings.*` array elements.


