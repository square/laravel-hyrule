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

    public static function dataValid()
    {
        return [
            [true],
            [false],
            [1],
            [0],
        ];
    }


    public static function dataInvalid()
    {
        return [
            ['true'],
            ['false'],
            [null],
            [2],
            [-1],
        ];
    }
}
