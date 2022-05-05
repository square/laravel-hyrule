<?php

declare(strict_types=1);

namespace Square\Hyrule\Tests\Validator;

use Illuminate\Validation\Validator;
use Square\Hyrule\Validator\ValidatesTopLevelRules;

class CustomValidator extends Validator
{
    use ValidatesTopLevelRules;
}