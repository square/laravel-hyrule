## Path Expressions with `Hyrule::pathExp()`


A bunch of Laravel rules has property paths as arguments e.g. `required_with:...`, `required_without:...`, etc. and only accepts the full property path in order to work e.g.

```php
[
    'person' => ['array:name,age'],
    'person.age' => ['integer', 'required'],
    'person.name' => ['required', 'array:first_name,last_name'],
    'person.name.first_name' => [ 'string', 'optional', 'max:255'],
    'person.name.last_name' => [
        'string',
        'max:255',
        'required_without:first_name',  // Wrong!
        'required_without:person.name.first_name',  // Correct!
    ],
]
```

For shallow and/or relatively simple structured data, it is not an issue other than being a bit unintuitive. But when dealing w/ complex, deeply-nested or repetitive structures, it gets real tricky real quick. These are the sort of use-cases `Hyrule::pathExp()` aims to solve:

```php
class PersonDataType
{
    // As expected, this magic method will be treated as the callable:
    public function __invoke(ObjectNode $person)
    {
        $person
            ->object('name')
                ->string('first_name')
                    ->optional()
                    ->max(255)
                    ->end()
                ->string('last_name')
                    ->max(255)
                    ->requiredWithout(
                        // Agnostic of the full property path
                        Hyrule::pathExp()->parent()->get('first_name')
                    )
                    ->end()
           ->end()
           ->integer('age')
            ->required()
    }
}

$rules = Hyrule::create()
    ->object('person')
        ->with(new PersonDataType())
        ->object('parents')
            ->object('mother')
                ->with(new PersonDataType())
            ->end()
            ->object('father')
                ->with(new PersonDataType())
            ->end()
        ->end()
    ->end()
->build();

// $rules:

[
    'person' => ['array:name,age'],
    'person.age' => ['integer', 'required'],
    'person.name' => ['required', 'array:first_name,last_name'],
    'person.name.first_name' => [ 'string', 'optional', 'max:255'],
    'person.name.last_name' => [
        'string',
        'max:255',
        'required_without:person.name.first_name',  // Correct!
    ],
    'person.parents' => ['array:mother,father'],
    'person.parents.mother' => ['array:name,age'],
    'person.parents.mother.age' => ['integer', 'required'],
    'person.parents.mother.name' => ['required', 'array:first_name,last_name'],
    'person.parents.mother.name.first_name' => [ 'string', 'optional', 'max:255'],
    'person.parents.mother.name.last_name' => [
        'string',
        'max:255',
        'required_without:person.parents.mother.name.first_name',  // Correct!
    ],
    'person.parents.father' => ['array:name,age'],
    'person.parents.father.age' => ['integer', 'required'],
    'person.parents.father.name' => ['required', 'array:first_name,last_name'],
    'person.parents.father.name.first_name' => [ 'string', 'optional', 'max:255'],
    'person.parents.father.name.last_name' => [
        'string',
        'max:255',
        'required_without:person.parents.father.name.first_name',  // Correct!
    ],
]
```

