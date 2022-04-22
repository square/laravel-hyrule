<?php

namespace Square\Hyrule\Tests;

use Generator;
use Square\Hyrule\Nodes\IntegerNode;

class IntegerNodeTest extends NodeTestAbstract
{

    protected function getBuilderMethodName(): string
    {
        return 'integer';
    }

    protected function getNodeClassName(): string
    {
        return IntegerNode::class;
    }

    /**
     * @return string[]
     */
    protected function defaultRules(): array
    {
        return ['integer'];
    }

    /**
     * @return array<array<mixed>>
     */
    public function dataValid()
    {
        return [
            [1],
            ['1'],
            [PHP_INT_MAX],
            [PHP_INT_MIN],
        ];
    }

    /**
     * @return Generator
     */
    public function dataInvalid()
    {
        yield 'string' => [
            'abc',
        ];

        yield 'float' => [
            1.1,
        ];

        yield 'boolean' => [
            false,
        ];

        yield 'array' => [
            [1],
        ];

        yield 'object' => [
            ['a' => 1],
        ];

        yield 'too small' => [
            1,
            static function (IntegerNode $node) {
                $node->min(2);
            },
        ];

        yield 'too large' => [
            10,
            static function (IntegerNode $node) {
                $node->max(9);
            },
        ];
    }
}
