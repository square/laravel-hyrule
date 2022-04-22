<?php

declare(strict_types=1);

namespace Square\Hyrule\Nodes;

class IntegerNode extends ScalarNode
{
    /**
     * @return string[]
     */
    protected function defaultRules(): array
    {
        return ['integer'];
    }
}
