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
        return ['numeric'];
    }

    /**
     * @return array<array<mixed>>
     */
    public static function dataValid()
    {
        return [
            [1],
            ['1'],
            [PHP_INT_MAX],
            [PHP_INT_MIN],
            [1.5],
            [1.0],
            ['100.0'],
            ['+0123.45e6'],
        ];
    }

    public static function dataInvalid()
    {
        return [
            ['1.1.2'],
            ['abc'],
            ['pi'],
            ['12,000']
        ];
    }
}
