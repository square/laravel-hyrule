## Custom validation for shape of an object

By default, Hyrule not only encourages you to validate the data-type of each field at a minimum
but also encourages you to only accept fields that you've explicitly allowed. This is accomplished by having the `array:field1,field2,...` rule added to all object/nested fields.

If you wish to override which rule to use, you can use the `ObjectNode#propertiesRule(...)` method:

```php

Hyrule::create()
    ->object('person')
        ->required()
        ->propertiesRule(new MatchesObjectShape('Person')) <- // A callable.
        // etc.
        
        
class MatchesObjectShape()
{
    protected $objectName;
    
    public function __construct(string $objectName)
    {
        $this->objectName = $objectName;
    }
    
    /**
     * Return a list of rule strings/Rule objects that will be attached to the
     * "person" field.
     */
    public function __invoke(ObjectNode $objectNode)
    {
         return [
            // Here we are using a custom rule object
            // that formats unknown fields in a much better way than array:field1,field2... does,
            // exactly to our liking.
            new MyCustomRule(
                $objectNode->getDefinedPropertyNames(),
                $this->objectName,
            ),
         ];
    }
}
```

It's kinda niche, but you might appreciate the flexibility.


