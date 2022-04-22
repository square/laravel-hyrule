<?php

declare(strict_types=1);

namespace Square\Hyrule\Nodes;

class FloatNode extends ScalarNode
{
    /**
     * @return string[]
     */
    protected function defaultRules(): array
    {
        return ['float'];
    }
}
