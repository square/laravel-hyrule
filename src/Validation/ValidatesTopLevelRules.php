<?php

namespace Square\Hyrule\Validation;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Validator;

trait ValidatesTopLevelRules
{
    /**
     * Get the value od a given attribute. Interpret "" as top-level data.
     * @param string $attribute
     * @return mixed
     */
    protected function getValue($attribute)
    {
        assert($this instanceof Validator);
        if ($attribute === '') {
            return $this->data;
        }
        return parent::getValue($attribute);
    }

    /**
     * Determine if the attribute is validatable. Allows "" attribute, which
     * will be interpreted as top-level data.
     *
     * @param Rule|string $rule
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    protected function isValidatable($rule, $attribute, $value)
    {
        assert($this instanceof Validator);
        if ($attribute === '') {
            return true;
        }
        return parent::isValidatable($rule, $attribute, $value);
    }
}