<?php

namespace Square\Hyrule\Tests;

use Square\Hyrule\Nodes\FloatNode;

class FloatNodeTest extends NodeTestAbstract
{
    protected function getBuilderMethodName(): string
    {
        return 'float';
    }

    protected function getNodeClassName(): string
    {
        return FloatNode::class;
    }

    /**
     * @return string[]
     */
    protected function defaultRules(): array
    {
        return ['float'];
    }
}
