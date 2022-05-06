<?php

namespace Square\Hyrule\Rules;

use BadMethodCallException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Square\Hyrule\Validation\Rules\KnownPropertiesOnly as BaseRule;

/**
 * We moved the implementation under a new namespace. This is here to keep backwards compatibility.
 */
class KnownPropertiesOnly extends BaseRule
{
}
