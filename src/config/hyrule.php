<?php

return [
    /**
     * The framework will be configured to return instances of this class when building
     * validators. This variant in particular is capable of evaluating top-level rules,
     * keyed by the "" field e.g.
     *
     *  ["" => new ValidatesEntireDataRule(...)]
     *
     * Possible values:
     *
     * 1.) Class name of any Validator class that uses the
     *     Square\Hyrule\Validator\ValidatesTopLevelRules
     * 2.) Anything else e.g. NULL/FALSE: Do not override existing behavior.
     */
    'use_strict_validator_class' => Square\Hyrule\Validator\StrictValidator::class,
];
