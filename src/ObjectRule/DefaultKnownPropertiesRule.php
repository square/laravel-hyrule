<?php

declare(strict_types=1);

namespace Square\Hyrule\ObjectRule;

use Illuminate\Contracts\Validation\Rule;
use Square\Hyrule\Nodes\ObjectNode;
use Square\Hyrule\Rules\KnownPropertiesOnly;

class DefaultKnownPropertiesRule
{
    /**
     * Called during ->build() of ObjectNode, responsible for returning validation rules against unknown properties.
     * @param ObjectNode $node
     * @return array<Rule>
     */
    public function __invoke(ObjectNode $node): array
    {
        return [
            new KnownPropertiesOnly($node->getDefinedPropertyNames()),
        ];
    }
}
