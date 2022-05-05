<?php

namespace Square\Hyrule\Validator;

use Illuminate\Validation\Validator;

class StrictValidator extends Validator
{
    use ValidatesTopLevelRules;
}