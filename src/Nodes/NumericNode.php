<?php

declare(strict_types=1);

namespace Square\Hyrule\Nodes;

class NumericNode extends ScalarNode
{
    /**
     * @return string[]
     */
    protected function defaultRules(): array
    {
        return ['numeric'];
    }
}
