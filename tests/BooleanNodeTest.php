<?php

namespace Square\Hyrule\Tests;

use Square\Hyrule\Nodes\BooleanNode;

class BooleanNodeTest extends NodeTestAbstract
{
    protected function getBuilderMethodName(): string
    {
        return 'boolean';
    }

    protected function getNodeClassName(): string
    {
        return BooleanNode::class;
    }

    /**
     * @return string[]
     */
    protected function defaultRules(): array
    {
        return ['boolean'];
    }
}
