<?php

namespace Square\Hyrule\Tests;

use Generator;
use Square\Hyrule\Nodes\NumericNode;

class NumericNodeTest extends NodeTestAbstract
{
    protected function getBuilderMethodName(): string
    {
        return 'numeric';
    }

    protected function getNodeClassName(): string
    {
        return NumericNode::class;
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
            ['1.1'],
            [3.14],
            [PHP_INT_MAX],
            [PHP_INT_MIN],
        ];
    }

    /**
     * @return Generator
     */
    public static function dataInvalid()
    {
        yield 'string' => [
            'a',
        ];

        yield 'boolean' => [
            false,
        ];

        yield 'array' => [
            [1],
        ];

        yield 'empty array' => [
            [],
        ];

        yield 'object' => [
            ['a' => 1],
        ];
    }
}
