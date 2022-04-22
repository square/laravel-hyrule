<?php

namespace Square\Hyrule\Rules;

use BadMethodCallException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Validation\ValidatorAwareRule;

class KnownPropertiesOnly implements Rule, ValidatorAwareRule
{
    /**
     * @var Validator
     */
    protected Validator $validator;

    /**
     * @var array<string>
     */
    private array $allowedProperties;

    /**
     * @param array<string> $allowedProperties
     */
    public function __construct(array $allowedProperties)
    {
        $this->allowedProperties = $allowedProperties;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_array($value)) {
            return true;
        }

        $keys = array_keys($value);

        $unknownProperties = array_diff($keys, $this->allowedProperties);
        foreach ($unknownProperties as $property) {
            $this->validator->addFailure(sprintf('%s.%s', $attribute, $property), 'unknown_property', [
                'property' => $property,
                'allowed_properties' => $this->allowedProperties,
            ]);
        }

        return true;
    }

    /**
     * @return array<string>|string
     */
    public function message()
    {
        throw new BadMethodCallException('Should not be called.');
    }

    /**
     * @param Validator $validator
     * @return Rule
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }
}
