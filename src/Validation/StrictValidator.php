<?php

namespace Square\Hyrule\Validation;

use Illuminate\Validation\Validator;

class StrictValidator extends Validator
{
    use ValidatesTopLevelRules;
}